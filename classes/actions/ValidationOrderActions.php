<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
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
        $this->conveyor['amount'] = $this->module->isZeroDecimalCurrency($this->context->currency->iso_code) ? $charges[0]->amount : $charges[0]->amount / 100;

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

        $response = \Stripe\Charge::create([
          'amount' => $this->module->isZeroDecimalCurrency($this->context->currency->iso_code) ? $amount : $amount * 100,
          'currency' => $this->context->currency->iso_code,
          'source' => $source,
        ]);

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
        $paymentIntentDatas = StripePaymentIntent::getDatasByIdPaymentIntent($this->conveyor['id_payment_intent']);
        $paymentIntent = new StripePaymentIntent($paymentIntentDatas['id_stripe_payment_intent']);
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
        if($this->conveyor['status'] != 'succeeded' && $this->conveyor['status'] != 'pending') {
            return false;
        }

        $message = 'Stripe Transaction ID: '.$this->conveyor['id_payment_intent'];

        $this->conveyor['source'] = \Stripe\Source::retrieve($this->conveyor['token']);

        $this->conveyor['secure_key'] = isset($this->context->customer->secure_key) ? $this->context->customer->secure_key : false;
        $paid = $this->module->isZeroDecimalCurrency($this->conveyor['paymentIntent']->getCurrency()) ? $this->conveyor['paymentIntent']->getAmount()*100 : $this->conveyor['paymentIntent']->getAmount();
        /* Add transaction on Prestashop back Office (Order) */
        if (!empty($source->type) && $source->type == 'sofort' && $this->conveyor['status'] == 'pending') {
            $orderStatus = Configuration::get('STRIPE_OS_SOFORT_WAITING');
            $this->conveyor['result'] = 4;
        } else {
            $orderStatus = Configuration::get('PS_OS_PAYMENT');
            $this->conveyor['result'] = 1;
        }
        try {
            $this->module->validateOrder(
                (int)$this->context->cart->id,
                (int)$orderStatus,
                $paid,
                $this->module->l('Payment by Stripe'),
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

        $intent = \Stripe\PaymentIntent::retrieve($this->conveyor['id_payment_intent']);
        $charges = $intent->charges->data;

        $cardType = $this->conveyor['source']->type;
        if (isset($this->conveyor['source']->card)) {
            $cardType = $this->conveyor['source']->card->brand;
        }

        $stripePayment = new StripePayment();
        $stripePayment->setIdStripe($this->conveyor['chargeId']);
        $stripePayment->setIdPaymentIntent($this->conveyor['id_payment_intent']);
        $stripePayment->setName($this->conveyor['source']->owner->name);
        $stripePayment->setIdCart((int)$this->context->cart->id);
        // $stripePayment->setLast4((int)$charges[0]->payment_method_details->card->last4);
        $stripePayment->setType(Tools::strtolower($cardType));
        $stripePayment->setAmount($this->conveyor['amount']);
        $stripePayment->setRefund((int)0);
        $stripePayment->setCurrency(Tools::strtolower($this->context->currency->iso_code));
        $stripePayment->setResult((int)$this->conveyor['result']);
        $stripePayment->setState((int)Configuration::get('STRIPE_MODE'));
        $stripePayment->setDateAdd(date("Y-m-d H:i:s"));
        $stripePayment->save();

        $orderId = Order::getOrderByCartId((int)$this->context->cart->id);
        $orderPaymentDatas = OrderPayment::getByOrderId($orderId);

        $orderPayment = new OrderPayment($orderPaymentDatas[0]->id);
        $orderPayment->transaction_id = $this->conveyor['chargeId'];
        $orderPayment->save();

        return true;
    }

}