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

use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_4_1($module)
{
    try {
        $installer = new Stripe_officialClasslib\Install\ModuleInstaller($module);

        if (!$installer->install()) {
            return false;
        }

        if (!Validate::isLoadedObject(new OrderState((int) Configuration::get(stripe_official::OS_SOFORT_WAITING)))) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'order_state` os
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT') . ')
            WHERE name LIKE "%Sofort%"');

            if (empty($result[0])) {
                return false;
            }

            Configuration::updateValue(stripe_official::OS_SOFORT_WAITING, $result[0]['id_order_state']);
        }

        return true;
    } catch (PrestaShopDatabaseException $e) {
        ProcessLoggerHandler::logError(
            $e->getMessage(),
            null,
            null,
            'Upgrade 2.4.1'
        );
        ProcessLoggerHandler::closeLogger();
        return false;
    } catch (PrestaShopException $e) {
        ProcessLoggerHandler::logError(
            $e->getMessage(),
            null,
            null,
            'Upgrade 2.4.1'
        );
        ProcessLoggerHandler::closeLogger();
        return false;
    }
}
