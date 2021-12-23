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

            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_state` WHERE id_order_state IN (' . implode(',', $order_state_to_delete) . ');')) {
                return false;
            }
        }

        if (Configuration::get(Stripe_official::OXXO_WAITING) == Configuration::get(Stripe_official::SEPA_DISPUTE)) {
            $stripe_order_states = [
                ['name' => 'sofort', 'color' => '#4169E1', 'config' => stripe_official::OS_SOFORT_WAITING],
                ['name' => 'stripe', 'color' => '#03befc', 'config' => stripe_official::CAPTURE_WAITING],
                ['name' => 'sepa', 'color' => '#fcba03', 'config' => stripe_official::SEPA_WAITING],
                ['name' => 'sepa', 'color' => '#e3e1dc', 'config' => stripe_official::SEPA_DISPUTE],
                ['name' => 'oxxo', 'color' => '#C23416', 'config' => stripe_official::OXXO_WAITING],
            ];

            $order_state_to_affect = array_map(
                'cleanStripeOrderState',
                array_column($stripe_order_states, 'name'),
                array_column($stripe_order_states, 'color')
            );

            for ($i = 0; $i < 5; $i++) {
                if (!$order_state_to_affect[$i]) {
                    return false;
                }

                $order_state = new OrderState($order_state_to_affect[$i]);
                $order_state->unremovable = true;
                $order_state->module_name = $module->name;
                $order_state->save();

                Configuration::updateValue($stripe_order_states[$i]['config'], $order_state->id);
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

/**
 * @throws PrestaShopDatabaseException
 */
function cleanStripeOrderState($name, $color)
{
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
        'SELECT os.id_order_state FROM `' . _DB_PREFIX_ . 'order_state` os
            INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.id_order_state = osl.id_order_state)
            WHERE osl.name LIKE "%' . $name . '%" AND os.color = "' . $color . '" GROUP BY os.id_order_state ORDER BY os.id_order_state DESC;');

    $order_state_ids = array_column($result, 'id_order_state');

    $order_state_id = $order_state_ids[0];

    if (count($order_state_ids) > 1) {
        unset($order_state_ids[0]);

        $order_state_to_clean = implode(',', $order_state_ids);

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE `' . _DB_PREFIX_ . 'order_history` SET id_order_state = ' . $order_state_id . ' WHERE id_order_state IN (' . $order_state_to_clean . ');')
            || !Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_state` WHERE id_order_state IN (' . $order_state_to_clean . ');')) {
            return false;
        }
    }

    return $order_state_id;
}
