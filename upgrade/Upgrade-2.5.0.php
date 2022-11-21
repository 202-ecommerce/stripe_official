<?php
/**
 * 2007-2022 Stripe
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
 * @license   Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Module $module
 * @return bool
 */
function upgrade_module_2_5_0($module)
{
    $is_valid = true;

    if (true === $module->isRegisteredInHook('header')) {
        $is_valid = $module->unregisterHook('header');
    }

    if (true === $module->isRegisteredInHook('orderConfirmation')) {
        $is_valid = $is_valid && $module->unregisterHook('orderConfirmation');
    }

    if (true === $module->isRegisteredInHook('adminOrder')) {
        $is_valid = $is_valid && $module->unregisterHook('adminOrder');
    }

    if (true === $module->isRegisteredInHook('displayMyAccountBlock')) {
        $is_valid = $is_valid && $module->unregisterHook('displayMyAccountBlock');
    }

    if (false === $is_valid) {
        return false;
    }

    return  $module->registerHook('displayHeader')
        &&  $module->registerHook('displayOrderConfirmation');
}
