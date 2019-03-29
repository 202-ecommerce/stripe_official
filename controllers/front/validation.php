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

        $response = Tools::getValue('response');

        $paymentIntentDatas = StripePaymentIntent::getDatasByIdPaymentIntent($response['paymentIntent']['id']);
        $paymentIntent = new StripePaymentIntent($paymentIntentDatas['id_stripe_payment_intent'],
                                                 $paymentIntentDatas['id_payment_intent'],
                                                 null,
                                                 $paymentIntentDatas['amount'],
                                                 $paymentIntentDatas['currency'],
                                                 $paymentIntentDatas['date_add'],
                                                 null);

        $paymentIntent->setStatus($response['paymentIntent']['status']);
        $paymentIntent->setDateUpd(date("Y-m-d H:i:s"));
        $paymentIntent->update();

        if($response['paymentIntent']['status'] == 'succeeded') {
            $params = array(
                'token' => $response['paymentIntent']['source'],
                'amount' => $paymentIntent->getAmount()*100,
                'currency' => $paymentIntent->getCurrency(),
                'cart_id' => $this->context->cart->id,
            );

            $chargeResult = $this->module->createOrder($response['paymentIntent'], $params);
        }

        $this->ajaxDie(Tools::jsonEncode($chargeResult));
    }
}