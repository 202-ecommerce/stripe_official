<?php

/**
 * 2007-2021 PrestaShop
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

/**
 * @throws \Stripe\Exception\ApiErrorException
 */
function upgrade_module_2_3_5($module)
{
    $context = Context::getContext();

    /* Clean all webhooks from stripe module in Live Mode */
    if (Configuration::get(Stripe_official::KEY)) {
        $stripeClient = new \Stripe\StripeClient(Configuration::get(Stripe_official::KEY));
        $webhooksList = $stripeClient->webhookEndpoints->all();
        foreach ($webhooksList as $webhookEndpoint) {
            if ($webhookEndpoint->url == $context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'))) {
                $webhookEndpoint->delete();
            }
        }
    }
    /* Clean all webhooks from stripe module in Test Mode */
    if (Configuration::get(Stripe_official::TEST_KEY)) {
        $stripeClient = new \Stripe\StripeClient(Configuration::get(Stripe_official::TEST_KEY));
        $webhooksList = $stripeClient->webhookEndpoints->all();
        foreach ($webhooksList as $webhookEndpoint) {
            if ($webhookEndpoint->url == $context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'))) {
                $webhookEndpoint->delete();
            }
        }
    }
    /* Create new webhook in current Mode */
    StripeWebhook::create();
    /* Delete (if exist) table stripe_webhook from previous module version */
    $sql = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'stripe_webhook;';
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    return true;
}
