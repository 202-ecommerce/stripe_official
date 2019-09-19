<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) Stripe
 * @license   Commercial license
 */

use Stripe_officialClasslib\Actions\DefaultActions;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Stripe_officialClasslib\Registry;

class ValidationOrderActions extends DefaultActions
{
    protected $context;
    protected $module;

    /*
        Input : 'source', 'response', 'context', 'module'
        Output : 'token', 'id_payment_intent', 'status', 'chargeId', 'amount'
     */
    public function prepareFlowNone()
    {
        $this->context = $this->conveyor['context'];
        $this->module = $this->conveyor['module'];

        $response = (object)$this->conveyor['response']['paymentIntent'];
        $intent = \Stripe\PaymentIntent::retrieve($response->id);
        $charges = $intent->charges->data;

        $this->conveyor['token'] = $response->source;
        $this->conveyor['id_payment_intent'] = $response->id;
        $this->conveyor['status'] = $response->status;
        $this->conveyor['chargeId'] = $charges[0]->id;

        if ($this->module->isZeroDecimalCurrency($this->context->currency->iso_code)) {
            $this->conveyor['amount'] = $charges[0]->amount;
        } else {
            $this->conveyor['amount'] = $charges[0]->amount / 100;
        }

        return true;
    }

    /*
        Input : 'source', 'response', 'context', 'module'
        Output : 'token', 'id_payment_intent', 'status', 'chargeId', 'amount'
     */
    public function prepareFlowRedirect()
    {
        $this->context = $this->conveyor['context'];
        $this->module = $this->conveyor['module'];
        $source = $this->conveyor['source'];

        $sourceRetrieve = \Stripe\Source::retrieve($source);
        ProcessLoggerHandler::logInfo($sourceRetrieve);
        ProcessLoggerHandler::closeLogger();

        if ($sourceRetrieve->status == 'failed') {
            ProcessLoggerHandler::logInfo($source . " => status failed", 'Cart', $this->context->cart->id);
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        $secret_key = $this->module->getSecretKey();

        \Stripe\Stripe::setApiKey($secret_key);

        $amount = $this->context->cart->getOrderTotal();
        if (!$this->module->isZeroDecimalCurrency($this->context->currency->iso_code)) {
            $amount = $amount * 100;
        }

        $response = \Stripe\Charge::create(array(
          'amount' => $amount,
          'currency' => $this->context->currency->iso_code,
          'source' => $source,
        ));

        $this->conveyor['token'] = $source;
        $this->conveyor['id_payment_intent'] = $response->source->metadata->paymentIntent;
        $this->conveyor['status'] = $response->status;
        $this->conveyor['chargeId'] = $response->id;
        $this->conveyor['amount'] = $amount;

        return true;
    }

    /*
        Input : 'id_payment_intent', 'status'
        Output : 'paymentIntent'
     */
    public function updatePaymentIntent()
    {
        $amount = $this->conveyor['amount'];
        if (strstr($this->conveyor['chargeId'], 'ch_')) {
            $amount = $amount*100;
        }

        $paymentIntent = new StripePaymentIntent();
        $paymentIntent->findByIdPaymentIntent($this->conveyor['id_payment_intent']);
        $paymentIntent->setAmount($amount);
        $paymentIntent->setStatus($this->conveyor['status']);
        $paymentIntent->setDateUpd(date("Y-m-d H:i:s"));

        $paymentIntent->update();

        $this->conveyor['paymentIntent'] = $paymentIntent;

        return true;
    }

    /*
        Input : 'status', 'id_payment_intent', 'token', 'paymentIntent'
        Output : 'source', 'secure_key', 'result'
    */
    public function createOrder()
    {
        if ($this->conveyor['status'] != 'succeeded' && $this->conveyor['status'] != 'pending') {
            return false;
        }

        $message = 'Stripe Transaction ID: '.$this->conveyor['id_payment_intent'];

        $this->conveyor['source'] = \Stripe\Source::retrieve($this->conveyor['token']);

        if (isset($this->context->customer->secure_key)) {
            $this->conveyor['secure_key'] = $this->context->customer->secure_key;
        } else {
            $this->conveyor['secure_key'] = false;
        }

        if ($this->module->isZeroDecimalCurrency($this->conveyor['paymentIntent']->getCurrency())) {
            $paid = $this->conveyor['paymentIntent']->getAmount()*100;
        } else {
            $paid = $this->conveyor['paymentIntent']->getAmount();
        }

        /* Add transaction on Prestashop back Office (Order) */
        if (!empty($this->conveyor['source']->type)
            && $this->conveyor['source']->type == 'sofort'
            && $this->conveyor['status'] == 'pending') {
            $orderStatus = Configuration::get('STRIPE_OS_SOFORT_WAITING');
            $this->conveyor['result'] = 4;
        } else {
            $orderStatus = Configuration::get('PS_OS_PAYMENT');
            $this->conveyor['result'] = 1;
        }
        $this->conveyor['cart'] = $this->context->cart;

        try {
            $this->module->validateOrder(
                (int)$this->conveyor['cart']->id,
                (int)$orderStatus,
                $paid,
                $this->module->l('Payment by Stripe', 'ValidationOrderActions'),
                $message,
                array(),
                null,
                false,
                $this->conveyor['secure_key']
            );
        } catch (PrestaShopException $e) {
            $this->_error[] = (string)$e->getMessage();
            return false;
        }

        unset($this->context->cookie->stripe_payment_intent);

        return true;
    }
    /*
        Input : 'id_payment_intent', 'source', 'result'
        Output :
    */
    public function addTentative()
    {
        if ($this->conveyor['source']->type == 'American Express') {
            $this->conveyor['source']->type = 'amex';
        } elseif ($this->conveyor['source']->type == 'Diners Club') {
            $this->conveyor['source']->type = 'diners';
        }

        if (!$this->module->isZeroDecimalCurrency($this->conveyor['source']->currency)) {
            $this->conveyor['source']->amount /= 100;
        }

        $cardType = $this->conveyor['source']->type;
        if (isset($this->conveyor['source']->card)) {
            $cardType = $this->conveyor['source']->card->brand;
        }

        $stripePayment = new StripePayment();
        $stripePayment->setIdStripe($this->conveyor['chargeId']);
        $stripePayment->setIdPaymentIntent($this->conveyor['id_payment_intent']);
        $stripePayment->setName($this->conveyor['source']->owner->name);
        $stripePayment->setIdCart((int)$this->context->cart->id);
        $stripePayment->setType(Tools::strtolower($cardType));
        $stripePayment->setAmount($this->conveyor['amount']);
        $stripePayment->setRefund((int)0);
        $stripePayment->setCurrency(Tools::strtolower($this->context->currency->iso_code));
        $stripePayment->setResult((int)$this->conveyor['result']);
        $stripePayment->setState((int)Configuration::get('STRIPE_MODE'));
        $stripePayment->setDateAdd(date("Y-m-d H:i:s"));
        $stripePayment->save();

        // Payent with Sofort is not accepted yet so we can't get his orderPayment
        if (Tools::strtolower($cardType) != 'sofort') {
            $orderId = Order::getOrderByCartId((int)$this->context->cart->id);
            $orderPaymentDatas = OrderPayment::getByOrderId($orderId);

            $orderPayment = new OrderPayment($orderPaymentDatas[0]->id);
            $orderPayment->transaction_id = $this->conveyor['chargeId'];
            $orderPayment->save();
        }

        return true;
    }


    public function chargeWebhook()
    {
        $this->context = $this->conveyor['context'];

        ProcessLoggerHandler::logInfo('chargeWebhook', null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();
        $this->conveyor['chargeId'] = $this->conveyor['event_json']->data->object->id;
        ProcessLoggerHandler::logInfo('chargeId => ' . $this->conveyor['chargeId'], null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();
        $stripe_payment = new StripePayment();
        $stripe_payment->getStripePaymentByCharge($this->conveyor['chargeId']);

        if ($stripe_payment->id == false) {
            ProcessLoggerHandler::logError('$stripe_payment->id = false', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        ProcessLoggerHandler::logInfo('$stripe_payment->id = OK', null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();

        $id_order = Order::getOrderByCartId($stripe_payment->id_cart);
        if ($id_order == false) {
            ProcessLoggerHandler::logError('$id_order = false', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        ProcessLoggerHandler::logInfo('$id_order = OK', null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();

        $order = new Order($id_order);

        ProcessLoggerHandler::logInfo('current charge => '.$this->conveyor['event_json']->type, null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();

        if ($this->conveyor['event_json']->type == 'charge.succeeded') {
            ProcessLoggerHandler::logInfo('setCurrentState for charge.succeeded', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
        } elseif ($this->conveyor['event_json']->type == 'charge.canceled') {
            ProcessLoggerHandler::logInfo('setCurrentState for charge.canceled', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
        } elseif ($this->conveyor['event_json']->type == 'charge.failed') {
            ProcessLoggerHandler::logInfo('setCurrentState for charge.failed', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
        }

        return true;
    }
}
