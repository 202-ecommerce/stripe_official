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

use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class StripeWebhook extends ObjectModel
{
    public static function create()
    {
        try {
            $shopGroupId = Stripe_official::getShopGroupIdContext();
            $shopId = Stripe_official::getShopIdContext();

            $stripeAccount = \Stripe\Account::retrieve();
            $webhookEndpoint = \Stripe\WebhookEndpoint::create([
                'url' => Stripe_official::getWebhookUrl(),
                'enabled_events' => Stripe_official::$webhook_events,
            ]);

            Configuration::updateValue(Stripe_official::WEBHOOK_SIGNATURE, $webhookEndpoint->secret, false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::WEBHOOK_ID, $webhookEndpoint->id, false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::ACCOUNT_ID, $stripeAccount->id, false, $shopGroupId, $shopId);

            return true;
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                'Create webhook endpoint - ' . (string) $e->getMessage(),
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
                    'limit' => 16,
                ]
            );
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                'getWebhookList - ' . (string) $e->getMessage(),
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
        $webhookUrl = Stripe_official::getWebhookUrl();
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
