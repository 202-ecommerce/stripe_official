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

class stripe_officialOrderFailureModuleFrontController extends ModuleFrontController
{

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setTemplate('module:stripe_official/views/templates/front/order-confirmation-failed-17.tpl');
        } else {
            $this->setTemplate('order-confirmation-failed-16.tpl');
        }
    }
}