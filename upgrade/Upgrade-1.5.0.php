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
