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

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($this->module && $this->module->active) {
var_dump(Context::getContext()->cart);
            if (Tools::getValue('checkOrder')) {
                $cart_id = $this->context->cart->id;
                $id_order = Order::getOrderByCartId($cart_id);
                
                $stripe_payment = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "stripe_payment` WHERE `id_cart` = '" . pSQL((int)$cart_id) . "'");
                
                if ($stripe_payment && ($stripe_payment['result'] == 1 || $stripe_payment['result'] == Stripe_official::_PENDING_SOFORT_)) {
                    if ($id_order) {
                        $url = $this->context->link->getPageLink(
                            'order-confirmation', 
                            true, 
                            null, 
                            array('id_cart' => (int)$cart_id, 'id_module' => (int)$this->module->id, 'id_order' => (int)$id_order, 'key' => $this->context->customer->secure_key)
                        );
                        $this->ajaxDie(Tools::jsonEncode(array('confirmation_url' => $url)));
                    } else {
                        $this->ajaxDie('continue');
                    }
                } else if ($stripe_payment && $stripe_payment['result'] == 0) {
                    $order_page = $this->context->link->getPageLink('order', true, null, array('step' => 3, 'stripe_failed' => true));
                    $this->ajaxDie(Tools::jsonEncode(array('error_url' => $order_page)));
                } else {
                    $this->ajaxDie('continue');
                }
            }

            /* Loading current billing address from PrestaShop */
            if (!isset($this->context->cart->id)
                || empty($this->context->cart->id)
                || !isset($this->context->cart->id_address_invoice)
                || empty($this->context->cart->id_address_invoice)
            ) {
                $this->ajaxDie('No active shopping cart');
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
                $this->module->chargev2($params);
            } else {
                $this->ajaxDie('ko');
            }
        }
    }
}
