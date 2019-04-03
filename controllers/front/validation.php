<?php
/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

class stripe_officialValidationModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ssl = true;
        $this->ajax = true;
        $this->json = true;
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();


        $source = Tools::getValue('source');

        if (!empty($source)) {
            $secret_key = $this->module->getSecretKey();

            \Stripe\Stripe::setApiKey($secret_key);

            $currency = $this->context->currency->iso_code;
            $amount = $this->context->cart->getOrderTotal();
            $amount = $this->module->isZeroDecimalCurrency($currency) ? $amount : $amount * 100;

            $response = \Stripe\Charge::create([
              'amount' => $amount,
              'currency' => $currency,
              'source' => $source,
            ]);

            $intent = \Stripe\PaymentIntent::retrieve($response->source->metadata->paymentIntent);

            $response->payment_intent = $intent;
            $token = $source;
            $id_payment_intent = $response->payment_intent->id;
        } else {
            $response = (object)Tools::getValue('response')['paymentIntent'];
            $token = $response->source;
            $id_payment_intent = $response->id;
        }

        $paymentIntentDatas = StripePaymentIntent::getDatasByIdPaymentIntent($id_payment_intent);
        $paymentIntent = new StripePaymentIntent($paymentIntentDatas['id_stripe_payment_intent']);
        $paymentIntent->setStatus($response->status);
        $paymentIntent->setDateUpd(date("Y-m-d H:i:s"));
        $paymentIntent->update();

        if($response->status == 'succeeded') {
            $params = array(
                'token' => $token,
                'amount' => $paymentIntent->getAmount()*100,
                'currency' => $paymentIntent->getCurrency(),
                'cart_id' => $this->context->cart->id,
                'id_payment_intent' => $id_payment_intent,
            );

            $chargeResult = $this->module->createOrder($response, $params);
        }

        if (!empty($response->source->flow) && $response->source->flow == 'redirect') {
            Tools::redirect($chargeResult['url']);
            exit;
        }
        $this->ajaxDie(Tools::jsonEncode($chargeResult));
    }
}