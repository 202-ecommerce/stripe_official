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
        try {
            $this->context = $this->conveyor['context'];
            $this->module = $this->conveyor['module'];

            if (isset($this->conveyor['response']['paymentIntent'])) {
                $response = (object)$this->conveyor['response']['paymentIntent'];
            } else {
                $response = (object)$this->conveyor['response'];
            }
            $intent = \Stripe\PaymentIntent::retrieve($response->id);
            $charges = $intent->charges->data;

            $this->conveyor['currency'] = $response->currency;
            $this->conveyor['token'] = $response->payment_method;
            $this->conveyor['id_payment_intent'] = $response->id;
            $this->conveyor['status'] = $response->status;
            if (!empty($charges)) {
                $this->conveyor['chargeId'] = $charges[0]->id;
            } else {
                $this->conveyor['chargeId'] = '';
            }

            if ($this->module->isZeroDecimalCurrency($response->currency)) {
                $this->conveyor['amount'] = $intent->amount;
            } else {
                $this->conveyor['amount'] = $intent->amount / 100;
            }

            ProcessLoggerHandler::logInfo(
                'prepareFlowNone : OK',
                null,
                null,
                'ValidationOrderActions - prepareFlowNone'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - prepareFlowNone'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    /*
        Input : 'source', 'response', 'context', 'module'
        Output : 'token', 'id_payment_intent', 'status', 'chargeId', 'amount'
     */
    public function prepareFlowRedirect()
    {
        try {
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

            $this->conveyor['currency'] = $response->currency;
            $this->conveyor['token'] = $source;
            $this->conveyor['id_payment_intent'] = $response->source->metadata->paymentIntent;
            $this->conveyor['status'] = $response->status;
            $this->conveyor['chargeId'] = $response->id;
            $this->conveyor['amount'] = $this->context->cart->getOrderTotal();

            ProcessLoggerHandler::logInfo(
                'prepareFlowRedirect : OK',
                null,
                null,
                'ValidationOrderActions - prepareFlowRedirect'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - prepareFlowRedirect'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    /*
        Input : 'id_payment_intent', 'context', 'module'
        Output : 'currency', token', 'status', 'chargeId', 'amount'
     */
    public function prepareFlowRedirectPaymentIntent()
    {
        try {
            $this->context = $this->conveyor['context'];
            $this->module = $this->conveyor['module'];

            $intent = \Stripe\PaymentIntent::retrieve($this->conveyor['id_payment_intent']);
            $charges = $intent->charges->data;

            // Payment failed for redirect payment methods
            if (empty($charges)) {
                return false;
            }

            $this->conveyor['currency'] = $charges[0]->currency;
            $this->conveyor['token'] = $charges[0]->payment_method;
            $this->conveyor['status'] = $charges[0]->status;
            $this->conveyor['chargeId'] = $charges[0]->id;

            if ($this->module->isZeroDecimalCurrency($charges[0]->currency)) {
                $this->conveyor['amount'] = $charges[0]->amount;
            } else {
                $this->conveyor['amount'] = $charges[0]->amount / 100;
            }

            ProcessLoggerHandler::logInfo(
                'prepareFlowRedirectPaymentIntent : OK',
                null,
                null,
                'ValidationOrderActions - prepareFlowRedirectPaymentIntent'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - prepareFlowRedirectPaymentIntent'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    /*
        Input : 'id_payment_intent', 'status'
        Output : 'paymentIntent'
     */
    public function updatePaymentIntent()
    {
        try {
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

            ProcessLoggerHandler::logInfo(
                'updatePaymentIntent : OK',
                null,
                null,
                'ValidationOrderActions - updatePaymentIntent'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - updatePaymentIntent'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    /*
        Input : 'status', 'id_payment_intent', 'token', 'paymentIntent'
        Output : 'source', 'secure_key', 'result'
    */
    public function createOrder()
    {
        if ($this->conveyor['status'] != 'succeeded'
            && $this->conveyor['status'] != 'pending'
            && $this->conveyor['status'] != 'requires_capture'
            && $this->conveyor['status'] != 'requires_action'
            && $this->conveyor['status'] != 'processing') {
            return false;
        }

        $message = 'Stripe Transaction ID: '.$this->conveyor['id_payment_intent'];

        if (strpos($this->conveyor['token'], 'pm_') !== false) {
            $this->conveyor['payment_method'] = \Stripe\PaymentMethod::retrieve($this->conveyor['token']);
            $this->conveyor['datas']['type'] = $this->conveyor['payment_method']->type;
            $this->conveyor['datas']['owner'] = $this->conveyor['payment_method']->billing_details->name;
        } else {
            $this->conveyor['source'] = \Stripe\Source::retrieve($this->conveyor['token']);
            $this->conveyor['datas']['type'] = $this->conveyor['source']->type;
            $this->conveyor['datas']['owner'] = $this->conveyor['source']->owner->name;
        }

        if (isset($this->context->customer->secure_key)) {
            $this->conveyor['secure_key'] = $this->context->customer->secure_key;
        } else {
            $this->conveyor['secure_key'] = false;
        }

        $paid = $this->context->cart->getOrderTotal();

        /* Add transaction on Prestashop back Office (Order) */
        if ($this->conveyor['status'] == 'requires_capture') {
            $orderStatus = Configuration::get('STRIPE_CAPTURE_WAITING');
            $this->conveyor['result'] = 2;
        } elseif ($this->conveyor['status'] == 'requires_action') {
            $orderStatus = Configuration::get('STRIPE_OXXO_WAITING');
            $this->conveyor['result'] = 2;
        } elseif (!empty($this->conveyor['datas']['type'])
            && $this->conveyor['datas']['type'] == 'sofort'
            && $this->conveyor['status'] == 'pending') {
            $orderStatus = Configuration::get('STRIPE_OS_SOFORT_WAITING');
            $this->conveyor['result'] = 4;
        } elseif ($this->conveyor['datas']['type'] == 'sepa_debit') {
            $orderStatus = Configuration::get(Stripe_official::SEPA_WAITING);
            $this->conveyor['result'] = 3;
        } else {
            $orderStatus = Configuration::get('PS_OS_PAYMENT');
            $this->conveyor['result'] = 1;
        }
        $this->conveyor['cart'] = $this->context->cart;

        ProcessLoggerHandler::logInfo(
            'create Stripe order : '.$this->conveyor['status'],
            null,
            null,
            'ValidationOrderActions - createOrder'
        );
        ProcessLoggerHandler::closeLogger();

        try {
            $this->module->validateOrder(
                (int)$this->conveyor['cart']->id,
                (int)$orderStatus,
                $paid,
                $this->module->l(Tools::ucfirst(Stripe_official::$paymentMethods[$this->conveyor['datas']['type']]['name']).' via Stripe', 'ValidationOrderActions'),
                $message,
                array(),
                null,
                false,
                $this->conveyor['secure_key']
            );

            ProcessLoggerHandler::logInfo(
                'Prestashop order created',
                null,
                null,
                'ValidationOrderActions - createOrder'
            );
            ProcessLoggerHandler::closeLogger();

            $idOrder = Order::getOrderByCartId((int)$this->conveyor['cart']->id);
            $order = new Order($idOrder);
            if (empty($this->conveyor['source'])) {
                \Stripe\PaymentIntent::update(
                    $this->conveyor['id_payment_intent'],
                    [
                        'description' => $this->context->shop->name.' '.$order->reference
                    ]
                );
            } else {
                \Stripe\Charge::update(
                    $this->conveyor['chargeId'],
                    [
                        'description' => $this->context->shop->name.' '.$order->reference
                    ]
                );
            }

            if ($this->conveyor['status'] == 'requires_capture') {
                $stripeCapture = new StripeCapture();
                $stripeCapture->id_payment_intent = $this->conveyor['id_payment_intent'];
                $stripeCapture->id_order = Order::getOrderByCartId((int)$this->conveyor['cart']->id);
                $stripeCapture->expired = 0;
                $stripeCapture->date_catch = date('Y-m-d H:i:s');
                $stripeCapture->save();
            }

            ProcessLoggerHandler::logInfo(
                'createOrder : OK',
                null,
                null,
                'ValidationOrderActions - createOrder'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - createOrder'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    public function sendMail()
    {
        try {
            if ($this->conveyor['payment_method']->type != 'oxxo') {
                return true;
            }

            $dir_mail = false;
            if (file_exists(dirname(__FILE__).'/../../mails/'.$this->context->language->iso_code.'/oxxo.txt') &&
                file_exists(dirname(__FILE__).'/../../mails/'.$this->context->language->iso_code.'/oxxo.html')) {
                $dir_mail = dirname(__FILE__).'/../../mails/';
            }

            $orderId = Order::getOrderByCartId((int)$this->context->cart->id);
            $order = new Order((int)$orderId);

            $template_vars = array(
                '{name}' => $this->conveyor['payment_method']->billing_details->name,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{order_id}' => $order->id,
                '{voucher_url}' => $this->conveyor['response']['paymentIntent']['next_action']['oxxo_display_details']['hosted_voucher_url'],
                '{order_ref}' => $order->reference,
                '{total_paid}' => Tools::displayPrice($order->total_paid, new Currency($order->id_currency)),
            );

            $this->conveyor['voucher_url'] = $this->conveyor['response']['paymentIntent']['next_action']['oxxo_display_details']['hosted_voucher_url'];
            $this->conveyor['voucher_expire'] = $this->conveyor['response']['paymentIntent']['next_action']['oxxo_display_details']['expires_after'];

            if ($dir_mail) {
                Mail::Send(
                    $this->context->language->id,
                    'oxxo',
                    sprintf(Mail::l('New order : #%d - %s', $this->context->language->id), $order->id, $order->reference),
                    $template_vars,
                    $this->conveyor['payment_method']->billing_details->email,
                    null,
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    $dir_mail,
                    null,
                    $this->context->shop->id
                );
            }

            ProcessLoggerHandler::logInfo(
                'sendMail : OK',
                null,
                null,
                'ValidationOrderActions - sendMail'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - sendMail'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    public function saveCard()
    {
        try {
            if ($this->conveyor['saveCard'] == 'false') {
                return true;
            }

            $stripeAccount = \Stripe\Account::retrieve();

            $stripeCustomer = new StripeCustomer();
            $stripeCustomer = $stripeCustomer->getCustomerById($this->context->customer->id, $stripeAccount->id);

            if ($stripeCustomer->id == null) {
                $customer = \Stripe\Customer::create([
                    'description' => 'Customer created from Prestashop Stripe module',
                    'email' => $this->context->customer->email,
                    'name' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                ]);

                $stripeCustomer->id_customer = $this->context->customer->id;
                $stripeCustomer->stripe_customer_key = $customer->id;
                $stripeCustomer->id_account = $stripeAccount->id;
                $stripeCustomer->save();
            }

            $customer = \Stripe\Customer::retrieve($stripeCustomer->stripe_customer_key);

            $stripeCard = new StripeCard();
            $stripeCard->stripe_customer_key = $customer->id;
            $stripeCard->payment_method = $this->conveyor['token'];
            if (!$stripeCard->save()) {
                ProcessLoggerHandler::logError(
                    'Error during save card, card has not been registered',
                    null,
                    null,
                    'StripeCard'
                );
            }

            ProcessLoggerHandler::logInfo(
                'saveCard : OK',
                null,
                null,
                'ValidationOrderActions - saveCard'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - saveCard'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    /*
        Input : 'id_payment_intent', 'source', 'result'
        Output :
    */
    public function addTentative()
    {
        try {
            if ($this->conveyor['datas']['type'] == 'American Express') {
                $this->conveyor['datas']['type'] = 'amex';
            } elseif ($this->conveyor['datas']['type'] == 'Diners Club') {
                $this->conveyor['datas']['type'] = 'diners';
            }

            $cardType = $this->conveyor['datas']['type'];
            if (isset($this->conveyor['payment_method']->card)) {
                $cardType = $this->conveyor['payment_method']->card->brand;
            }

            $stripePayment = new StripePayment();
            $stripePayment->setIdStripe($this->conveyor['chargeId']);
            $stripePayment->setIdPaymentIntent($this->conveyor['id_payment_intent']);
            $stripePayment->setName($this->conveyor['datas']['owner']);
            $stripePayment->setIdCart((int)$this->context->cart->id);
            $stripePayment->setType(Tools::strtolower($cardType));
            $stripePayment->setAmount($this->conveyor['amount']);
            $stripePayment->setRefund((int)0);
            $stripePayment->setCurrency(Tools::strtolower($this->context->currency->iso_code));
            $stripePayment->setResult((int)$this->conveyor['result']);
            $stripePayment->setState((int)Configuration::get('STRIPE_MODE'));
            if (isset($this->conveyor['voucher_url']) && isset($this->conveyor['voucher_expire'])) {
                $stripePayment->setVoucherUrl($this->conveyor['voucher_url']);
                $stripePayment->setVoucherExpire(date("Y-m-d H:i:s", $this->conveyor['voucher_expire']));
            }
            $stripePayment->setDateAdd(date("Y-m-d H:i:s"));
            $stripePayment->save();

            // Payent with Sofort is not accepted yet so we can't get his orderPayment
            if (Tools::strtolower($cardType) != 'sofort') {
                $orderId = Order::getOrderByCartId((int)$this->context->cart->id);
                $orderPaymentDatas = OrderPayment::getByOrderId($orderId);

                if (empty($orderPaymentDatas[0]) || empty($orderPaymentDatas[0]->id)) {
                    ProcessLoggerHandler::logError(
                        'OrderPayment is not created due to a PrestaShop, please verify order state configuration is loggable (Consider the associated order as validated). We try to create one with charge id ' .$this->conveyor['chargeId'] . ' on payment.',
                        'Order',
                        $orderId,
                        'validation'
                    );
                    $order = new Order($orderId);
                    if (!$order->addOrderPayment($this->conveyor['amount'], null, $this->conveyor['chargeId'])) {
                        ProcessLoggerHandler::logError(
                            'PaymentModule::validateOrder - Cannot save Order Payment',
                            'Order',
                            $orderId,
                            'validation'
                        );
                        ProcessLoggerHandler::closeLogger();
                        PrestaShopLogger::addLog(
                            'PaymentModule::validateOrder - Cannot save Order Payment',
                            3,
                            null,
                            'Cart',
                            (int)$this->context->cart->id,
                            true
                        );
                        throw new PrestaShopException('Can\'t save Order Payment');
                    }
                    ProcessLoggerHandler::closeLogger();
                    return true;
                }

                $orderPayment = new OrderPayment($orderPaymentDatas[0]->id);
                $orderPayment->transaction_id = $this->conveyor['chargeId'];
                $orderPayment->save();
            }

            ProcessLoggerHandler::logInfo(
                'addTentative : OK',
                null,
                null,
                'ValidationOrderActions - addTentative'
            );
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                preg_replace("/\n/", '<br>', (string)$e->getMessage().'<br>'.$e->getTraceAsString()),
                null,
                null,
                'ValidationOrderActions - addTentative'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        return true;
    }

    public function chargeWebhook()
    {
        $this->context = $this->conveyor['context'];
        $this->conveyor['IdPaymentIntent'] = $this->conveyor['event_json']->data->object->payment_intent;

        ProcessLoggerHandler::logInfo(
            'chargeWebhook with IdPaymentIntent => ' . $this->conveyor['IdPaymentIntent'],
            null,
            null,
            'webhook'
        );
        $stripe_payment = new StripePayment();
        $stripe_payment->getStripePaymentByPaymentIntent($this->conveyor['IdPaymentIntent']);

        if ($stripe_payment->id == false) {
            ProcessLoggerHandler::logError(
                '$stripe_payment->id = false',
                null,
                null,
                'webhook'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        ProcessLoggerHandler::logInfo(
            '$stripe_payment->id = OK',
            'StripePayment',
            $stripe_payment->id,
            'webhook'
        );

        $id_order = Order::getOrderByCartId($stripe_payment->id_cart);
        if ($id_order == false) {
            ProcessLoggerHandler::logError(
                '$id_order = false',
                null,
                null,
                'webhook'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        ProcessLoggerHandler::logInfo(
            '$id_order = OK',
            'Order',
            $id_order,
            'webhook'
        );

        $order = new Order($id_order);

        if ($this->conveyor['events_states'][$this->conveyor['event_json']->type] == $order->getCurrentState()) {
            ProcessLoggerHandler::logInfo(
                'Order status is already the good one',
                null,
                null,
                'webhook'
            );
            ProcessLoggerHandler::closeLogger();
            return true;
        }

        ProcessLoggerHandler::logInfo(
            'current charge => '.$this->conveyor['event_json']->type,
            null,
            null,
            'webhook'
        );

        if ($this->conveyor['event_json']->type == 'charge.dispute.created') {
            $order->setCurrentState(Configuration::get(Stripe_official::SEPA_DISPUTE));
        } elseif ($this->conveyor['event_json']->type == 'charge.captured') {
            $history = new OrderHistory();
            $history->id_order = (int) $order->id;
            $history->id_employee = 0;
            $history->changeIdOrderState((int)Configuration::get('PS_OS_PAYMENT'), (int) $order->id, true);

            $query = new DbQuery();
            $query->select('invoice_number, invoice_date, delivery_number, delivery_date');
            $query->from('orders');
            $query->where('id_order = ' . pSQL($order->id));
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());

            $order->invoice_date = $res['invoice_date'];
            $order->invoice_number = $res['invoice_number'];
            $order->delivery_date = $res['delivery_date'];
            $order->delivery_number = $res['delivery_number'];
            $order->update();

            $history->addWithemail();
        } elseif ($this->conveyor['event_json']->type == 'charge.expired'
            || $this->conveyor['event_json']->type == 'charge.refunded') {
            $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
        } elseif ($this->conveyor['event_json']->type == 'charge.succeeded') {
            $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
            if ($this->conveyor['event_json']->data->object->payment_method_details->type == 'oxxo') {
                $stripePayment = new StripePayment();
                $stripePayment->getStripePaymentByPaymentIntent($this->conveyor['IdPaymentIntent']);
                $stripePayment->setIdStripe($this->conveyor['event_json']->data->object->id);
                $stripePayment->setVoucherValidate(date("Y-m-d H:i:s"));
                $stripePayment->save();

                ProcessLoggerHandler::logInfo(
                    'oxxo charge ID => '.$this->conveyor['event_json']->data->object->id,
                    null,
                    null,
                    'webhook'
                );
            }
        } elseif ($this->conveyor['event_json']->type == 'charge.failed') {
            $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
        }

        ProcessLoggerHandler::logInfo(
            'setCurrentState for '.$this->conveyor['event_json']->type,
            'Order',
            $id_order,
            'webhook'
        );
        ProcessLoggerHandler::closeLogger();
        return true;
    }
}
