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

            $intent = \Stripe\PaymentIntent::retrieve($this->conveyor['paymentIntent']);

            ProcessLoggerHandler::logInfo(
                '$intent : '.$intent,
                null,
                null,
                'ValidationOrderActions - prepareFlowNone'
            );

            if (isset($intent->charges->data[0])) {
                $charges = $intent->charges->data[0];
                $this->conveyor['chargeId'] = $charges->id;
                $this->conveyor['token'] = $charges->payment_method;
                $this->conveyor['status'] = $charges->status;
            } else {
                // for OXXO
                $this->conveyor['chargeId'] = '';
                $this->conveyor['token'] = $intent->payment_method;
                $this->conveyor['status'] = $intent->status;
            }

            $this->conveyor['currency'] = $intent->currency;
            $this->conveyor['saveCard'] = $intent->setup_future_usage;

            $stripeIdempotencyKey = new StripeIdempotencyKey();
            $stripeIdempotencyKey->getByIdPaymentIntent($intent->id);
            $this->conveyor['id_cart'] = $stripeIdempotencyKey->id_cart;

            if ($this->module->isZeroDecimalCurrency($intent->currency)) {
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

            return false;

            $this->conveyor['currency'] = $response->currency;
            $this->conveyor['token'] = $source;
            $this->conveyor['id_payment_intent'] = $response->source->metadata->paymentIntent;
            $this->conveyor['status'] = $response->status;
            $this->conveyor['chargeId'] = $response->id;
            $this->conveyor['amount'] = $this->context->cart->getOrderTotal();
            $this->conveyor['saveCard'] = null;
            $this->conveyor['id_cart'] = $this->context->cart->id;

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

            $intent = \Stripe\PaymentIntent::retrieve($this->conveyor['paymentIntent']);
            $charges = $intent->charges->data;

            // Payment failed for redirect payment methods
            if (empty($charges)) {
                return false;
            }

            $stripeIdempotencyKey = new StripeIdempotencyKey();
            $stripeIdempotencyKey->getByIdPaymentIntent($intent->id);
            $this->conveyor['id_cart'] = $stripeIdempotencyKey->id_cart;

            $this->conveyor['currency'] = $charges[0]->currency;
            $this->conveyor['token'] = $charges[0]->payment_method;
            $this->conveyor['status'] = $charges[0]->status;
            $this->conveyor['chargeId'] = $charges[0]->id;
            $this->conveyor['saveCard'] = null;

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
            $paymentIntent->findByIdPaymentIntent($this->conveyor['paymentIntent']);
            $paymentIntent->setAmount($amount);
            $paymentIntent->setStatus($this->conveyor['status']);
            $paymentIntent->setDateUpd(date("Y-m-d H:i:s"));

            $paymentIntent->update();

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
        $this->conveyor['isAlreadyOrdered'] = (bool)Order::getOrderByCartId((int)$this->conveyor['id_cart']);

        if ($this->conveyor['isAlreadyOrdered']) {
            ProcessLoggerHandler::logInfo(
                'Prestashop order has been already created for this cart',
                null,
                null,
                'ValidationOrderActions - createOrder'
            );
            return true;
        }

        if ($this->conveyor['status'] != 'succeeded'
            && $this->conveyor['status'] != 'pending'
            && $this->conveyor['status'] != 'requires_capture'
            && $this->conveyor['status'] != 'requires_action'
            && $this->conveyor['status'] != 'processing') {
            return false;
        }

        $message = 'Stripe Transaction ID: '.$this->conveyor['paymentIntent'];

        if (strpos($this->conveyor['token'], 'pm_') !== false) {
            $this->conveyor['payment_method'] = \Stripe\PaymentMethod::retrieve($this->conveyor['token']);
            $this->conveyor['datas']['type'] = $this->conveyor['payment_method']->type;
            $this->conveyor['datas']['owner'] = $this->conveyor['payment_method']->billing_details->name;
        } else {
            $this->conveyor['source'] = \Stripe\Source::retrieve($this->conveyor['token']);
            $this->conveyor['datas']['type'] = $this->conveyor['source']->type;
            $this->conveyor['datas']['owner'] = $this->conveyor['source']->owner->name;
        }

        $this->conveyor['cart'] = new Cart((int)$this->conveyor['id_cart']);

        $paid = $this->conveyor['amount'];

        /* Add transaction on Prestashop back Office (Order) */
        if ($this->conveyor['datas']['type'] == 'card' && Configuration::get(Stripe_official::CATCHANDAUTHORIZE) == 'on') {
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

        ProcessLoggerHandler::logInfo(
            'Beginning of validateOrder',
            null,
            null,
            'ValidationOrderActions - createOrder'
        );

        try {
            $addressDelivery = new Address($this->conveyor['cart']->id_address_delivery);
            $this->context->country = new Country($addressDelivery->id_country);

            ProcessLoggerHandler::logInfo(
                'Paid Amount => '.$paid,
                null,
                null,
                'ValidationOrderActions - createOrder'
            );

            $this->module->validateOrder(
                $this->conveyor['cart']->id,
                (int)$orderStatus,
                $paid,
                sprintf(
                    $this->module->l('%s via Stripe', 'ValidationOrderActions'),
                    Tools::ucfirst(Stripe_official::$paymentMethods[$this->conveyor['datas']['type']]['name'])
                ),
                $message,
                array(),
                null,
                false,
                $this->conveyor['cart']->secure_key
            );

            ProcessLoggerHandler::logInfo(
                'Prestashop order created',
                null,
                null,
                'ValidationOrderActions - createOrder'
            );

            $idOrder = Order::getOrderByCartId($this->conveyor['cart']->id);
            $order = new Order($idOrder);

            // capture payment for card if no catch and authorize enabled
            $intent = \Stripe\PaymentIntent::retrieve($this->conveyor['paymentIntent']);
            ProcessLoggerHandler::logInfo(
                'payment method => '.$intent->payment_method_types[0],
                null,
                null,
                'ValidationOrderActions - createOrder'
            );

            if ($intent->payment_method_types[0] == 'card' && Configuration::get(Stripe_official::CATCHANDAUTHORIZE) == null) {
                ProcessLoggerHandler::logInfo(
                    'Capturing card',
                    null,
                    null,
                    'ValidationOrderActions - createOrder'
                );

                $currency = new Currency($order->id_currency, $this->context->language->id, $this->context->shop->id);

                $amount = $this->module->isZeroDecimalCurrency($currency->iso_code) ? $order->getTotalPaid() : $order->getTotalPaid() * 100;

                ProcessLoggerHandler::logInfo(
                    'Capture Amount => '.$amount,
                    null,
                    null,
                    'ValidationOrderActions - createOrder'
                );

                $paid = $this->module->isZeroDecimalCurrency($currency->iso_code) ? $paid : $paid * 100;

                if ($amount != $paid) {
                    ProcessLoggerHandler::logInfo(
                        'Fix invalid amount "'.$amount.'" to "'.$paid,
                        null,
                        null,
                        'ValidationOrderActions - createOrder'
                    );
                    $amount = $paid;
                }

                if (!$this->module->captureFunds($amount, $this->conveyor['paymentIntent'])) {
                    ProcessLoggerHandler::closeLogger();
                    return false;
                }

                ProcessLoggerHandler::logInfo(
                    'Payment captured',
                    null,
                    null,
                    'ValidationOrderActions - createOrder'
                );
            }
            // END capture payment for card if no catch and authorize enabled

            if (empty($this->conveyor['source'])) {
                \Stripe\PaymentIntent::update(
                    $this->conveyor['paymentIntent'],
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
                $stripeCapture->id_payment_intent = $this->conveyor['paymentIntent'];
                $stripeCapture->id_order = Order::getOrderByCartId($this->conveyor['cart']->id);
                $stripeCapture->expired = 0;
                $stripeCapture->date_catch = date('Y-m-d H:i:s');
                $stripeCapture->save();
            }

            ProcessLoggerHandler::logInfo(
                'createOrder : OK',
                Order::class,
                $order->id,
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
            if ($this->conveyor['datas']['type'] != 'oxxo') {
                return true;
            }

            $dir_mail = false;
            if (file_exists(dirname(__FILE__).'/../../mails/'.$this->context->language->iso_code.'/oxxo.txt') &&
                file_exists(dirname(__FILE__).'/../../mails/'.$this->context->language->iso_code.'/oxxo.html')) {
                $dir_mail = dirname(__FILE__).'/../../mails/';
            }

            $orderId = Order::getOrderByCartId((int)$this->conveyor['id_cart']);
            $order = new Order((int)$orderId);

            if (!isset($this->conveyor['voucher_url']))
                $this->conveyor['voucher_url'] = $this->conveyor['event_json']->data->object->next_action->oxxo_display_details->hosted_voucher_url;
            if (!isset($this->conveyor['voucher_expire']))
                $this->conveyor['voucher_expire'] = $this->conveyor['event_json']->data->object->next_action->oxxo_display_details->expires_after;

            $template_vars = array(
                '{name}' => $this->conveyor['payment_method']->billing_details->name,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{order_id}' => $order->id,
                '{voucher_url}' => $this->conveyor['voucher_url'],
                '{order_ref}' => $order->reference,
                '{total_paid}' => Tools::displayPrice($order->total_paid, new Currency($order->id_currency)),
            );

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
            if (empty($this->conveyor['saveCard']) === true || empty($this->conveyor['cart']->id_customer) === true) {
                return true;
            }

            $cartCustomer = new Customer($this->conveyor['cart']->id_customer);

            $stripeAccount = \Stripe\Account::retrieve();

            $stripeCustomer = new StripeCustomer();
            $stripeCustomer = $stripeCustomer->getCustomerById($cartCustomer->id, $stripeAccount->id);

            if (empty($stripeCustomer->id) === true) {
                $customer = \Stripe\Customer::create([
                    'description' => 'Customer created from Prestashop Stripe module',
                    'email' => $cartCustomer->email,
                    'name' => $cartCustomer->firstname.' '.$cartCustomer->lastname,
                ]);

                $stripeCustomer->id_customer = $cartCustomer->id;
                $stripeCustomer->stripe_customer_key = $customer->id;
                $stripeCustomer->id_account = $stripeAccount->id;
                $stripeCustomer->save();
            }

            $stripeCard = new StripeCard();
            $stripeCard->stripe_customer_key = $stripeCustomer->stripe_customer_key;
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
        if ($this->conveyor['isAlreadyOrdered']) {
            return true;
        }

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
            $stripePayment->setIdPaymentIntent($this->conveyor['paymentIntent']);
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

            // Payment with Sofort is not accepted yet, so we can't get his orderPayment
            if (Tools::strtolower($cardType) != 'sofort'
                && Tools::strtolower($cardType) != 'sepa_debit'
                && Tools::strtolower($cardType) != 'oxxo') {
                $orderId = Order::getOrderByCartId((int)$this->context->cart->id);
                $orderPaymentDatas = OrderPayment::getByOrderId($orderId);

                if (empty($orderPaymentDatas[0]) || empty($orderPaymentDatas[0]->id)) {
                    ProcessLoggerHandler::logError(
                        'OrderPayment is not created due to a PrestaShop, please verify order state configuration is loggable (Consider the associated order as validated). We try to create one with charge id ' .$this->conveyor['chargeId'] . ' on payment.',
                        'Order',
                        $orderId,
                        'ValidationOrderActions - addTentative'
                    );
                    $order = new Order($orderId);
                    if (!$order->addOrderPayment($this->conveyor['amount'], null, $this->conveyor['chargeId'])) {
                        ProcessLoggerHandler::logError(
                            'PaymentModule::validateOrder - Cannot save Order Payment',
                            'Order',
                            $orderId,
                            'ValidationOrderActions - addTentative'
                        );
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

        $this->conveyor['IdPaymentIntent'] =
            (isset($this->conveyor['event_json']->data->object->payment_intent))
                ? $this->conveyor['event_json']->data->object->payment_intent
                : $this->conveyor['event_json']->data->object->id;
        ProcessLoggerHandler::logInfo(
            'chargeWebhook with IdPaymentIntent => ' . $this->conveyor['IdPaymentIntent'],
            null,
            null,
            'ValidationOrderActions - chargeWebhook'
        );

        $id_cart = $this->conveyor['event_json']->data->object->metadata->id_cart;
        $id_order = Order::getOrderByCartId($id_cart);
        $event_type = $this->conveyor['event_json']->type;

        if ($id_order == false) {
            if (in_array($event_type, Stripe_official::$webhook_events)) {
                ProcessLoggerHandler::logInfo(
                    'Unknown order => '.$event_type,
                    null,
                    null,
                    'ValidationOrderActions - chargeWebhook'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(200);
                return true;
            } else {
                ProcessLoggerHandler::logError(
                    'Unknown order => $id_order = false',
                    null,
                    null,
                    'ValidationOrderActions - chargeWebhook'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(200);
                return false;
            }
        }

        $order = new Order($id_order);
        ProcessLoggerHandler::logInfo(
            '$id_order = OK',
            'Order',
            $id_order,
            'ValidationOrderActions - chargeWebhook'
        );

        if ($order->module != 'stripe_official') {
            ProcessLoggerHandler::logInfo(
                'This order #'.$id_order.' was made with '.$order->module.' not with Stripe',
                null,
                null,
                'ValidationOrderActions - chargeWebhook'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
            return true;
        }

        if ($event_type != 'payment_intent.requires_action'
            && $this->conveyor['events_states'][$event_type] == $order->getCurrentState()) {
            ProcessLoggerHandler::logInfo(
                'Order status is already the good one',
                null,
                null,
                'ValidationOrderActions - chargeWebhook'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
            return true;
        }

        ProcessLoggerHandler::logInfo(
            'current charge => '.$event_type,
            null,
            null,
            'ValidationOrderActions - chargeWebhook'
        );


        switch($event_type)
        {
            case 'charge.succeeded':
                if ($order->getCurrentState() != Configuration::get('PS_OS_OUTOFSTOCK_PAID')) {
                    $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));

                    if ($this->conveyor['event_json']->data->object->payment_method_details->type == 'oxxo') {
                        $stripePayment = new StripePayment();
                        $stripePayment->getStripePaymentByPaymentIntent($this->conveyor['IdPaymentIntent']);
                        $stripePayment->setIdStripe($this->conveyor['event_json']->data->object->id);
                        $stripePayment->setVoucherValidate(date("Y-m-d H:i:s"));
                        $stripePayment->save();

                        ProcessLoggerHandler::logInfo(
                            'oxxo charge ID => ' . $this->conveyor['event_json']->data->object->id,
                            null,
                            null,
                            'ValidationOrderActions - chargeWebhook'
                        );
                    }
                }
                break;

            case 'charge.captured':
                if ($order->getCurrentState() == Configuration::get('STRIPE_CAPTURE_WAITING')) {
                    $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                }
                break;

            case 'charge.refunded':
                if ($order->getCurrentState() != Configuration::get('PS_OS_PAYMENT')) {
                    $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                    ProcessLoggerHandler::logInfo(
                        'Order canceled',
                        null,
                        null,
                        'ValidationOrderActions - chargeWebhook'
                    );
                } else {
                    if ($this->conveyor['event_json']->data->object->amount_refunded !== $this->conveyor['event_json']->data->object->amount_captured
                        || $this->conveyor['event_json']->data->object->amount_refunded !== $this->conveyor['event_json']->data->object->amount) {
                        $order->setCurrentState(Configuration::get('PS_CHECKOUT_STATE_PARTIAL_REFUND'));
                        ProcessLoggerHandler::logInfo(
                            'Partial refund of payment => ' . $this->conveyor['event_json']->data->object->id,
                            null,
                            null,
                            'ValidationOrderActions - chargeWebhook'
                        );
                    } else {
                        $order->setCurrentState(Configuration::get('PS_OS_REFUND'));
                        ProcessLoggerHandler::logInfo(
                            'Full refund of payment => ' . $this->conveyor['event_json']->data->object->id,
                            null,
                            null,
                            'ValidationOrderActions - chargeWebhook'
                        );
                    }
                }
                break;

            case 'charge.failed':
            case 'charge.expired':
                if ($order->getCurrentState() != Configuration::get('PS_OS_PAYMENT')) {
                    $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
                }
                break;

            case 'charge.dispute.created':
                $order->setCurrentState(Configuration::get(Stripe_official::SEPA_DISPUTE));
                break;

            default:
                return true;
        }

        $currentOrderState = $order->getCurrentOrderState()->name[(int)Configuration::get('PS_LANG_DEFAULT')];

        ProcessLoggerHandler::logInfo(
            'Set Order State to ' . $currentOrderState . ' for ' . $event_type,
            'Order',
            $id_order,
            'ValidationOrderActions - chargeWebhook'
        );
        ProcessLoggerHandler::closeLogger();
        return true;
    }
}
