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

require_once dirname(__FILE__) . '/../classes/StripeWebhook.php';

function upgrade_module_2_3_3($module)
{
    $context = Context::getContext();

    $installer = new Stripe_officialClasslib\Install\ModuleInstaller($module);
    $installer->installObjectModel('StripeWebhook');

    Configuration::deleteByName('STRIPE_WEBHOOK_SIGNATURE');

    foreach (Shop::getShops() as $shop) {
        if ($secret_key_test = Configuration::get(Stripe_official::TEST_KEY, $context->language->id, $shop['id_shop_group'], $shop['id_shop'])) {
            \Stripe\Stripe::setApiKey($secret_key_test);

            if (StripeWebhook::countWebhooksList() < 16) {
                $webhooksList = StripeWebhook::getWebhookList();

                $webhook_exists = false;
                foreach ($webhooksList as $webhookEndpoint) {
                    if ($webhookEndpoint->url == $context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'))) {
                        $stripeWebhook = new StripeWebhook();
                        $stripeWebhook->getByWebHookId($webhookEndpoint->id);
                        if (!Validate::isLoadedObject($stripeWebhook)) {
                            $webhookEndpoint->delete();
                            StripeWebhook::create($secret_key_test);
                        }
                        $webhook_exists = true;
                    }
                }

                if ($webhook_exists === false) {
                    StripeWebhook::create($secret_key_test);
                }
            }
        }

        if ($secret_key_live = Configuration::get(Stripe_official::KEY, $context->language->id, $shop['id_shop_group'], $shop['id_shop'])) {
            \Stripe\Stripe::setApiKey($secret_key_live);

            if (StripeWebhook::countWebhooksList() < 16) {
                $webhooksList = StripeWebhook::getWebhookList();

                $webhook_exists = false;
                foreach ($webhooksList as $webhookEndpoint) {
                    if ($webhookEndpoint->url == $context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'))) {
                        $stripeWebhook = new StripeWebhook();
                        $stripeWebhook->getByWebHookId($webhookEndpoint->id);
                        if (!Validate::isLoadedObject($stripeWebhook)) {
                            $webhookEndpoint->delete();
                            StripeWebhook::create($secret_key_live);
                        }
                        $webhook_exists = true;
                    }
                }

                if ($webhook_exists === false) {
                    StripeWebhook::create($secret_key_live);
                }
            }
        }
    }

    return true;
}
