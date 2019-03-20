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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_0($module)
{
    if (!Configuration::updateValue('STRIPE_ENABLE_IDEAL', 0)
        || !Configuration::updateValue('STRIPE_ENABLE_SOFORT', 0)
        || !Configuration::updateValue('STRIPE_ENABLE_GIROPAY', 0)
        || !Configuration::updateValue('STRIPE_ENABLE_BANCONTACT', 0)) {
        return false;
    }
    // Registration order status
    if (!$module->installOrderState()) {
        return false;
    }
    return true;
}
