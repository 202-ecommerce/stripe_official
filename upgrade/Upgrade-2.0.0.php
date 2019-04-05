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

function upgrade_module_2_0_0($module)
{
    $module->updateConfigurationKey('_PS_STRIPE_mode', 'STRIPE_MODE');
    $module->updateConfigurationKey('_PS_STRIPE_partial_refund_state', 'STRIPE_PARTIAL_REFUND_STATE');
    $module->updateConfigurationKey('_PS_STRIPE_refund_mode', 'STRIPE_REFUND_MODE');
    $module->updateConfigurationKey('_PS_STRIPE_test_key', 'STRIPE_TEST_KEY');
    $module->updateConfigurationKey('_PS_STRIPE_test_publishable', 'STRIPE_TEST_PUBLISHABLE');
    $module->updateConfigurationKey('_PS_STRIPE_key', 'STRIPE_KEY');
    $module->updateConfigurationKey('_PS_STRIPE_publishable', 'STRIPE_PUBLISHABLE');

    Configuration::deleteByName('_PS_STRIPE_secure');

    $installer = new Stripe_officialClasslib\Install\ModuleInstaller($module);
    $installer->installObjectModel('StripePaymentIntent');

    return true;
}
