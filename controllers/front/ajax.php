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

class Stripe_officialAjaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (!$this->module->active) {
          return;
        }

        $this->context = Context::getContext();

        if (Tools::getValue('checkOrder')) {
            $cart_id = Tools::getValue('cart_id');
            $link = Context::getContext()->link;
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'stripe_payment
                    WHERE `id_cart` = ' . (int)$cart_id;
            $stripe_payment = Db::getInstance()->getRow($sql);
            $id_order = Order::getOrderByCartId($cart_id);
            if ($stripe_payment && ($stripe_payment['result'] == 1
            || $stripe_payment['result'] == Stripe_official::_PENDING_SOFORT_)) {
                if ($id_order) {
                    $url = ($link->getPageLink('order-confirmation', true).'
                    ?id_cart='.(int)$cart_id.'
                    &id_module='.(int)$this->module->id.'
                    &id_order='.(int)$id_order.'
                    &key='.$this->context->customer->secure_key);
                    die(Tools::jsonEncode(array('confirmation_url' => $url)));
                } else {
                    die('continue');
                }
            } elseif ($stripe_payment && $stripe_payment['result'] == 0) {
                $order_page = Configuration::get('PS_ORDER_PROCESS_TYPE') ?
                $this->context->link->getPageLink('order-opc', true, null, array('stripe_failed'=>true)):
                $this->context->link->getPageLink('order', true, null, array('step'=>3, 'stripe_failed'=>true));
                die(Tools::jsonEncode(array('error_url' => $order_page)));
            } else {
                die('continue');
            }
        }

        /* Loading current billing address from PrestaShop */
        if (!isset($this->context->cart->id) || empty($this->context->cart->id) ||
            !isset($this->context->cart->id_address_invoice) ||
            empty($this->context->cart->id_address_invoice) ) {
            die('No active shopping cart');
        }

        $amount = $this->context->cart->getOrderTotal();

        if (!$this->isZeroDecimalCurrency($this->context->currency->iso_code)) {
            $amount *= 100;
        }

        $params = array(
            'token' => Tools::getValue('stripeToken'),
            'amount' => $amount,
            'currency' => $this->context->currency->iso_code,
            'cardHolderName' => Tools::getValue('cardHolderName'),
            'cardHolderEmail' => Tools::getValue('cardHolderEmail'),
            'type' => Tools::getValue('cardType'),
        );

        if (isset($params['token']) && !empty($params['token'])) {
            $this->module->chargev2($params);
        } else {
            die('ko');
        }
    }
}
