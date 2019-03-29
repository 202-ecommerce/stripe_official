<?php
/**
 * 2007-2018 PrestaShop
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

if (!defined('STRIPE_LOGGER_ENABLED')) {
    define('STRIPE_LOGGER_ENABLED', false);
}
/**
 * Registry of default pre-configured Prestashop logger
 */
class StripeLogger
{

    /**
     * @var PrestaShop logger
     */
    private static $logger = null;

    /**
     *
     */
    public static function getInstance()
    {
        if (self::$logger === null) {
            $logger = new \FileLogger();
            $logger->setFilename(_PS_CACHE_DIR_ . '../../logs/' . date("Ymd") . '_stripe.log');
            self::$logger = $logger;
        }

        return self::$logger;
    }

    public static function logInfo($msg)
    {
        if (STRIPE_LOGGER_ENABLED == true) {
            self::getInstance();
            self::$logger->logInfo($msg);
        }
    }

    public static function logError($msg)
    {
        if (STRIPE_LOGGER_ENABLED == true) {
            self::getInstance();
            self::$logger->logError($msg);
        }
    }
}
