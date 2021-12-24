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

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT os.id_order_state FROM `' . _DB_PREFIX_ . 'order_state` os
            INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.id_order_state = osl.id_order_state)
            WHERE osl.name LIKE "%stripe%" AND os.color = "#FFDD99" GROUP BY os.id_order_state;');

        if (!empty($result)) {
            $order_state_to_delete = array_column($result, 'id_order_state');

            foreach ($order_state_to_delete as $id) {
                $order_state = new OrderState((int) $id);
                $order_state->delete();
            }
        }

        if (Configuration::get(Stripe_official::OXXO_WAITING) == Configuration::get(Stripe_official::SEPA_DISPUTE)) {
            $stripe_order_states = [
                ['sofort', '#4169E1', stripe_official::OS_SOFORT_WAITING],
                ['stripe', '#03befc', stripe_official::CAPTURE_WAITING],
                ['sepa', '#fcba03', stripe_official::SEPA_WAITING],
                ['sepa', '#e3e1dc', stripe_official::SEPA_DISPUTE],
                ['oxxo', '#C23416', stripe_official::OXXO_WAITING],
            ];

            foreach ($stripe_order_states as list($name, $color, $config)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                    'SELECT os.id_order_state FROM `' . _DB_PREFIX_ . 'order_state` os
                    INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.id_order_state = osl.id_order_state)
                    WHERE osl.name LIKE "%' . pSQL($name) . '%" AND os.color = "' . pSQL($color) . '" GROUP BY os.id_order_state ORDER BY os.id_order_state DESC;');

                if (empty($result)) {
                    break;
                }

                $order_state_ids = array_column($result, 'id_order_state');

                foreach ($order_state_ids as $key => $order_state_id) {
                    $order_state = new OrderState((int) $order_state_id);

                    if ($key > 0) {
                        $order_state->delete();
                    } else {
                        $order_state->unremovable = true;
                        $order_state->module_name = 'stripe_official';
                        $order_state->save();

                        Configuration::updateValue($config, $order_state->id);
                    }
                }
            }
        }

        return true;
    } catch (PrestaShopDatabaseException $e) {
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
            $e->getMessage(),
            null,
            null,
            'Upgrade 2.4.1'
        );
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();
        return false;
    } catch (PrestaShopException $e) {
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
            $e->getMessage(),
            null,
            null,
            'Upgrade 2.4.1'
        );
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();
        return false;
    }
}
