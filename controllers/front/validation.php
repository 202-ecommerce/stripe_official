<?php
/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

class stripe_officialValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public $display_column_left = false;
    public function initContent()
    {

        parent::initContent();
        $stripe_client_secret = Tools::getValue('client_secret');
        $stripe_source = Tools::getValue('source');
        if (Configuration::get('_PS_STRIPE_mode') == 1) {
            $pubKey = Configuration::get('_PS_STRIPE_test_publishable');
        } else {
            $pubKey = Configuration::get('_PS_STRIPE_publishable');
        }
        $order_page = Configuration::get('PS_ORDER_PROCESS_TYPE') ? $this->context->link->getPageLink('order-opc', true, null, array('stripe_failed'=>true)):$this->context->link->getPageLink('order', true, null, array('step'=>3, 'stripe_failed'=>true));
        Context::getContext()->smarty->assign(array(
            'stripe_source' => $stripe_source,
            'stripe_client_secret' => $stripe_client_secret,
            'publishableKey' => $pubKey,
            'ajaxUrlStripe' => Context::getContext()->link->getModuleLink('stripe_official', 'ajax', array(), true),
            'module_dir' => _PS_MODULE_DIR_,
            'return_order_page' => $order_page,
        ));
        Context::getContext()->controller->addJs('https://js.stripe.com/v2/');
        Context::getContext()->controller->addJS(_PS_MODULE_DIR_.'stripe_official/views/js/jquery.the-modal.js');
        Context::getContext()->controller->addCSS(_PS_MODULE_DIR_.'stripe_official/views/css/the-modal.css', 'all');
        Context::getContext()->controller->addCSS(_PS_MODULE_DIR_.'stripe_official/views/css/front.css', 'all');
        Context::getContext()->controller->addJS(_PS_MODULE_DIR_.'stripe_official/views/js/payment_validation.js');

        return $this->setTemplate('payment_validation.tpl');

    }
}
