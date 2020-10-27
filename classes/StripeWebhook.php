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

use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class StripeWebhook extends ObjectModel
{
    public static function create()
    {
        try {
            $context = Context::getContext();

            $webhookEndpoint = \Stripe\WebhookEndpoint::create([
                'url' => $context->link->getModuleLink(
                    'stripe_official',
                    'webhook',
                    array(),
                    true,
                    Configuration::get('PS_LANG_DEFAULT'),
                    Configuration::get('PS_SHOP_DEFAULT')
                ),
                'enabled_events' => Stripe_official::$webhook_events,
            ]);

            Configuration::updateValue(Stripe_official::WEBHOOK_SIGNATURE, $webhookEndpoint->secret);
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                'Create webhook endpoint - '.(string)$e->getMessage(),
                null,
                null,
                'StripeWebhook'
            );
            return false;
        }
    }

    public static function getWebhookList()
    {
        try {
            return \Stripe\WebhookEndpoint::all(
                [
                    'limit' => 16
                ]
            );
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                'getWebhookList - '.(string)$e->getMessage(),
                null,
                null,
                'StripeWebhook'
            );
            return false;
        }
    }

    public static function countWebhooksList()
    {
        $list = self::getWebhookList();
        return count($list->data);
    }

    public static function webhookCanBeRegistered()
    {
        $context = Context::getContext();

        if (Stripe_official::isWellConfigured() === false) {
            return false;
        }

        $webhooksList = self::getWebhookList();
        $webhookUrl = $context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'));
        $webhookExists = false;

        foreach ($webhooksList->data as $webhook) {
            if ($webhook->url == $webhookUrl) {
                $webhookExists = true;
                break;
            }
        }

        if (self::countWebhooksList() >= 16 && $webhookExists === false) {
            return false;
        }

        return true;
    }
}
