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
    /** @var string */
    public $stripe_webhook_id;
    /** @var string */
    public $stripe_webhook_secret;
    /** @var string */
    public $stripe_secret_key;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_webhook',
        'primary'      => 'id_stripe_webhook',
        'fields'       => array(
            'stripe_webhook_id' => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
            'stripe_webhook_secret' => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
            'stripe_secret_key' => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
        ),
    );

    public function setStripeWebhookId($stripe_webhook_id)
    {
        $this->stripe_webhook_id = $stripe_webhook_id;
    }

    public function getStripeWebhookId()
    {
        return $this->stripe_webhook_id;
    }

    public function setStripeWebhookSecret($stripe_webhook_secret)
    {
        $this->stripe_webhook_secret = $stripe_webhook_secret;
    }

    public function getStripeWebhookSecret()
    {
        return $this->stripe_webhook_secret;
    }

    public function setStripeSecretKey($stripe_secret_key)
    {
        $this->stripe_secret_key = $stripe_secret_key;
    }

    public function getStripeSecretKey()
    {
        return $this->stripe_secret_key;
    }

    public function getByWebHookId($webhook_id)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('stripe_webhook_id = "'.pSQL($webhook_id).'"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function getBySecretKey($secret_key)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('stripe_secret_key = "'.pSQL($secret_key).'"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

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

            if (Configuration::get(Stripe_official::MODE) == '1') {
                $secret_key = Configuration::get(Stripe_official::TEST_KEY);
            } else {
                $secret_key = Configuration::get(Stripe_official::KEY);
            }

            $stripeWebhook = new StripeWebhook();
            $stripeWebhook->stripe_webhook_id = $webhookEndpoint->id;
            $stripeWebhook->stripe_webhook_secret = $webhookEndpoint->secret;
            $stripeWebhook->stripe_secret_key = $secret_key;
            $stripeWebhook->save();
        } catch (Exception $e) {
            ProcessLoggerHandler::logError(
                'Create webhook endpoint - '.(string)$e->getMessage(),
                null,
                null,
                'StripeWebhook'
            );
            ProcessLoggerHandler::closeLogger();
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
            ProcessLoggerHandler::closeLogger();
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
        $webhookUrl = $context->link->getModuleLink(
            'stripe_official',
            'webhook',
            array(),
            true,
            Configuration::get('PS_LANG_DEFAULT'),
            Configuration::get('PS_SHOP_DEFAULT')
        );
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
