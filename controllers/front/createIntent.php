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

class stripe_officialCreateIntentModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        try {
            if (Configuration::get(Stripe_official::CATCHANDAUTHORIZE) && Tools::getValue('payment_option') == 'card') {
                $capture_method = 'manual';
            } else {
                $capture_method = 'automatic';
            }

            $datasIntent = array(
                "amount" => Tools::getValue('amount'),
                "currency" => Tools::getValue('currency'),
                "payment_method_types" => array(Tools::getValue('payment_option')),
                "capture_method" => $capture_method
            );

            if (!Tools::getValue('id_payment_method')) {
                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $firstname = str_replace('"', '\\"', $this->context->customer->firstname);
                    $lastname = str_replace('"', '\\"', $this->context->customer->lastname);
                    $stripe_fullname = $firstname . ' ' . $lastname;
                } else {
                    $firstname = str_replace('\'', '\\\'', $this->context->customer->firstname);
                    $lastname = str_replace('\'', '\\\'', $this->context->customer->lastname);
                    $stripe_fullname = $firstname . ' ' . $lastname;
                }

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
                        'name' => $stripe_fullname
                    )
                );
            } else {
                $payment_method = Tools::getValue('id_payment_method');
                $stripeCustomer = new StripeCustomer();
                $customer = $stripeCustomer->getCustomerById($this->context->customer->id);
                $datasIntent['customer'] = $customer->stripe_customer_key;
            }

            $cardPayment = array(
                'payment_method' => $payment_method,
            );
            $saveCard = false;

            if (((Tools::getValue('card_form_payment') == 'true' && Tools::getValue('save_card_form') == 'true')
                || (Tools::getValue('card_form_payment') == 'true' && Tools::getValue('stripe_auto_save_card') == 'true')
                && (!Tools::getValue('id_payment_method') || Tools::getValue('payment_request') == 'true')
                && Tools::getValue('payment_option') == 'card')) {
                $cardPayment['setup_future_usage'] = 'on_session';
                $saveCard = true;
            } elseif (Tools::getValue('payment_option') != 'card') {
                $stripe_validation_return_url = $this->context->link->getModuleLink(
                    'stripe_official',
                    'validation',
                    array(),
                    true
                );
                $cardPayment['return_url'] = $stripe_validation_return_url;
            }

            $intent = \Stripe\PaymentIntent::create(array(
                $datasIntent
            ));

            // Keep the payment intent ID in session
            $this->context->cookie->stripe_payment_intent = $intent->id;

            $paymentIntent = new StripePaymentIntent();
            $paymentIntent->setIdPaymentIntent($intent->id);
            $paymentIntent->setStatus($intent->status);
            $paymentIntent->setAmount($intent->amount);
            $paymentIntent->setCurrency($intent->currency);
            $paymentIntent->setDateAdd(date("Y-m-d H:i:s", $intent->created));
            $paymentIntent->setDateUpd(date("Y-m-d H:i:s", $intent->created));
            $paymentIntent->save(false, false);
        } catch (Exception $e) {
            error_log($e->getMessage());
            die($e->getMessage());
        }

        echo Tools::jsonEncode(array(
            'intent' => $intent,
            'cardPayment' => $cardPayment,
            'saveCard' => $saveCard
        ));
        exit;
    }
}
