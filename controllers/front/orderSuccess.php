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

class stripe_officialOrderSuccessModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $datas = array(
            'payment_method' => Tools::getValue('payment_method')
        );

        if (Tools::getValue('payment_method') == 'oxxo') {
            $datas['voucher_url'] = Tools::getValue('voucher_url');
        }

        $this->context->smarty->assign($datas);

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setTemplate('module:stripe_official/views/templates/front/order-confirmation-success-17.tpl');
        } else {
            $this->setTemplate('order-confirmation-success-16.tpl');
        }
    }
}
