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

function upgrade_module_1_6_0($module)
{
    if (!$module->updateConfigurationKey('_PS_STRIPE_mode', 'STRIPE_MODE', 0)
        || !$module->updateConfigurationKey('_PS_STRIPE_partial_refund_state', 'STRIPE_PARTIAL_REFUND_STATE', 18)
        || !$module->updateConfigurationKey('_PS_STRIPE_refund_mode', 'STRIPE_REFUND_MODE', 1)
        || !$module->updateConfigurationKey('_PS_STRIPE_secure', 'STRIPE_SECURE', 1)
        || !$module->updateConfigurationKey('_PS_STRIPE_test_key', 'STRIPE_TEST_KEY', null)
        || !$module->updateConfigurationKey('_PS_STRIPE_test_publishable', 'STRIPE_TEST_PUBLISHABLE', null)
        || !$module->updateConfigurationKey('_PS_STRIPE_key', 'STRIPE_KEY', null)
        || !$module->updateConfigurationKey('_PS_STRIPE_publishable', 'STRIPE_PUBLISHABLE', null)) {
        return false;
    }

    if (!Configuration::updateValue('STRIPE_ENABLE_APPLEPAY', 0)
        || !Configuration::updateValue('STRIPE_ENABLE_GOOGLEPAY', 0)
        || !Configuration::updateValue('STRIPE_MINIMUM_AMOUNT_3DS', 50)) {
        return false;
    }

    return true;
}
