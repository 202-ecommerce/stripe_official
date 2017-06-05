<?php
/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

class Stripe_officialAjaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    private $stripe;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->stripe = Module::getInstanceByName('stripe_official');
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($this->stripe && $this->stripe->active) {
            $this->context = Context::getContext();

            /* Loading current billing address from PrestaShop */
            if (!isset($this->context->cart->id)
                || empty($this->context->cart->id)
                || !isset($this->context->cart->id_address_invoice)
                || empty($this->context->cart->id_address_invoice)
            ) {
                die('No active shopping cart');
            }

            $amount = $this->context->cart->getOrderTotal();

            // @see: https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
            $zeroDecimalCurrencies = array(
                'BIF',
                'CLP',
                'DJF',
                'GNF',
                'JPY',
                'KMF',
                'KRW',
                'MGA',
                'PYG',
                'RWF',
                'VND',
                'VUV',
                'XAF',
                'XOF',
                'XPF'
            );

            if (!in_array($this->context->currency->iso_code, $zeroDecimalCurrencies)) {
                $amount *= 100;
            }

            $params = array(
                'token' => Tools::getValue('stripeToken'),
                'amount' => $amount,
                'currency' => $this->context->currency->iso_code,
                'cardHolderName' => Tools::getValue('cardHolderName'),
                'type' => Tools::getValue('cardType'),
            );

            if (isset($params['token']) && !empty($params['token'])) {
                $this->stripe->chargev2($params);
            } else {
                die('ko');
            }
        }
    }
}