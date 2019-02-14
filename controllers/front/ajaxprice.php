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

class Stripe_officialAjaxpriceModuleFrontController extends ModuleFrontController
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

        die($amount);
    }
}
