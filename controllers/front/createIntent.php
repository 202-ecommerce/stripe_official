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

use Stripe\PaymentIntent;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialCreateIntentModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        ProcessLoggerHandler::logInfo(
            '[ Intent Creation Beginning ]',
            null,
            null,
            'createIntent - intiContent'
        );

        try {
            $cart = $this->context->cart;

            $currency = new Currency($cart->id_currency);
            $amount = $cart->getOrderTotal();
            $amount = Tools::ps_round($amount, 2);
            $amount = $this->module->isZeroDecimalCurrency($currency->iso_code) ? $amount : $amount * 100;

            $paymentOption = Tools::getValue('payment_option');
            $paymentMethodId = Tools::getValue('id_payment_method');

            $intentData = $this->constructIntentData($amount, $currency->iso_code, $paymentOption, $paymentMethodId);

            $cardData = $this->constructCardData($paymentMethodId);

            $intent = $this->createIdempotencyKey($intentData);
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                "Retrieve Stripe Account Error => ".$e->getMessage(),
                null,
                null,
                'createIntent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        }

        ProcessLoggerHandler::logInfo(
            '[ Intent Creation Ending ]',
            null,
            null,
            'createIntent - intiContent'
        );
        ProcessLoggerHandler::closeLogger();

        echo(
            json_encode([
                'intent' => $intent,
                'cardPayment' => $cardData['cardPayment'],
                'saveCard' => $cardData['save_card']
            ])
        );
        exit;
    }

    private function constructIntentData($amount, $currency, $paymentOption, $paymentMethodId)
    {
        try {
            $captureMethod = ($paymentOption == 'card') ? 'manual' : 'automatic';
            $customerFullName = $this->getCustomerFullNameContext();

            $shippingAddress = new Address($this->context->cart->id_address_delivery);
            $shippingAddressState = new State($shippingAddress->id_state);

            $intentData = array(
                "amount" => $amount,
                "currency" => $currency,
                "payment_method_types" => [$paymentOption],
                "capture_method" => $captureMethod,
                "metadata" => [
                    'id_cart' => $this->context->cart->id
                ],
                "description" => 'Product Purchase',
                'shipping' => [
                    'name' => $customerFullName,
                    'address' => [
                        'line1' => $shippingAddress->address1,
                        'postal_code' => $shippingAddress->postcode,
                        'city' => $shippingAddress->city,
                        'state' => $shippingAddressState->iso_code,
                        'country' => Country::getIsoById($shippingAddress->id_country),
                    ],
                ],
            );

            if ($paymentMethodId) {
                $stripeAccount = \Stripe\Account::retrieve();
                $stripeCustomer = new StripeCustomer();
                $customer = $stripeCustomer->getCustomerById($this->context->customer->id, $stripeAccount->id);
                $intentData['customer'] = $customer->stripe_customer_key;
            }

            ProcessLoggerHandler::logInfo(
                'Intent Data => '.Tools::jsonEncode($intentData),
                null,
                null,
                'createIntent - constructIntentData'
            );

            return $intentData;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            ProcessLoggerHandler::logError(
                "Retrieve Stripe Account Error => ".$e->getMessage(),
                null,
                null,
                'createIntent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        } catch (PrestaShopDatabaseException $e) {
            ProcessLoggerHandler::logError(
                "Retrieve Prestashop State Error => ".$e->getMessage(),
                null,
                null,
                'createIntent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        } catch (PrestaShopException $e) {
            ProcessLoggerHandler::logError(
                "Retrieve Prestashop State Error => ".$e->getMessage(),
                null,
                null,
                'createIntent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        }
    }

    private function constructCardData($paymentMethodId)
    {
        if (!$paymentMethodId) {
            $address = new Address($this->context->cart->id_address_invoice);

            $payment_method = array(
                'billing_details' => array(
                    'address' => array(
                        'city' => $address->city,
                        'country' => Country::getIsoById($address->id_country),
                        'line1' => $address->address1,
                        'line2' => $address->address2,
                        'postal_code' => $address->postcode
                    ),
                    'email' => $this->context->customer->email,
                    'name' => $this->getCustomerFullNameContext()
                )
            );
        } else {
            $payment_method = $paymentMethodId;
        }

        $cardData['cardPayment']['payment_method'] = $payment_method;
        $cardData['save_card'] = false;

        if (((Tools::getValue('card_form_payment') == 'true' && Tools::getValue('save_card_form') == 'true')
            || (Tools::getValue('card_form_payment') == 'true' && Tools::getValue('stripe_auto_save_card') == 'true')
            && (!Tools::getValue('id_payment_method') || Tools::getValue('payment_request') == 'true')
            && Tools::getValue('payment_option') == 'card')) {
            $cardData['cardPayment']['setup_future_usage'] = 'on_session';
            $cardData['save_card'] = true;
        } elseif (Tools::getValue('payment_option') != 'card') {
            $stripe_validation_return_url = $this->context->link->getModuleLink(
                'stripe_official',
                'orderConfirmationReturn',
                array(
                    'id_cart' => $this->context->cart->id
                ),
                true
            );
            $cardData['cardPayment']['return_url'] = $stripe_validation_return_url;
        }

        ProcessLoggerHandler::logInfo(
            'Card Payment => '.Tools::jsonEncode($cardData),
            null,
            null,
            'createIntent - constructCardPaymentData'
        );

        return $cardData;
    }

    private function createIdempotencyKey($intentData)
    {
        try {
            $cart = $this->context->cart;
            $stripeIdempotencyKey = new StripeIdempotencyKey();
            $stripeIdempotencyKey = $stripeIdempotencyKey->getByIdCart($cart->id);

            $paymentIntentStatus = (empty($stripeIdempotencyKey->id) === false) ? PaymentIntent::retrieve($stripeIdempotencyKey->id_payment_intent)->status : null;
            $updatableStatus = ['requires_payment_method', 'requires_confirmation', 'requires_action'];

            if (in_array($paymentIntentStatus, $updatableStatus) === false) {
                $intent = $stripeIdempotencyKey->createNewOne($cart->id, $intentData);
                $this->registerStripeEvent($intent);

                ProcessLoggerHandler::logInfo(
                    'Create New Intent => '.$intent,
                    null,
                    null,
                    'createIntent - createIdempotencyKey'
                );
            } else {
                unset($intentData['capture_method']);
                $intent = $stripeIdempotencyKey->updateIntentData($intentData);

                ProcessLoggerHandler::logInfo(
                    'Update Previous Intent => '.$intent,
                    null,
                    null,
                    'createIntent - createIdempotencyKey'
                );
            }

            return $intent;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            ProcessLoggerHandler::logError(
                "Create Stripe Intent Error => ".$e->getMessage(),
                null,
                null,
                'createIntent - createIdempotencyKey'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die($e->getMessage());
        } catch (PrestaShopException $e) {
            ProcessLoggerHandler::logError(
                "Save Stripe Idempotency Key Error => ".$e->getMessage(),
                null,
                null,
                'createIntent - createIdempotencyKey'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        }
    }

    private function registerStripeEvent($intent)
    {
        try {
            $stripeEventDate = new DateTime();
            $stripeEventDate = $stripeEventDate->setTimestamp($intent->created);

            $stripeEvent = new StripeEvent();
            $stripeEvent->setIdPaymentIntent($intent->id);
            $stripeEvent->setStatus(StripeEvent::CREATED_STATUS);
            $stripeEvent->setDateAdd($stripeEventDate->format('Y-m-d H:i:s'));
            $stripeEvent->setIsProcessed(1);
            $stripeEvent->setFlowType('direct');

            if ($stripeEvent->save()) {
                ProcessLoggerHandler::logInfo(
                    'Register created Stripe event status for payment intent '.$intent->id,
                    'StripeEvent',
                    $stripeEvent->id,
                    'createIntent - registerStripeEvent'
                );
            } else {
                ProcessLoggerHandler::logInfo(
                    'An issue appears during saving Stripe module event in database (the event probably already exists).',
                    null,
                    null,
                    'createIntent - registerStripeEvent'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(400);
                die('An unexpected problem has occurred. Please contact the support.');
            }
        } catch (PrestaShopException $e) {
            ProcessLoggerHandler::logError(
                "An issue appears during saving Stripe module event in database",
                null,
                null,
                'createIntent - registerStripeEvent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('An unexpected problem has occurred. Please contact the support.');
        }
    }

    private function getCustomerFullNameContext()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $firstname = str_replace('"', '\\"', $this->context->customer->firstname);
            $lastname = str_replace('"', '\\"', $this->context->customer->lastname);
        } else {
            $firstname = str_replace('\'', '\\\'', $this->context->customer->firstname);
            $lastname = str_replace('\'', '\\\'', $this->context->customer->lastname);
        }

        return $firstname . ' ' . $lastname;
    }
}
