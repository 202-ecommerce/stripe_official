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

require_once dirname(__FILE__) . '/../classes/StripeIdempotencyKey.php';

function upgrade_module_2_3_1($module)
{
    $installer = new Stripe_officialClasslib\Install\ModuleInstaller($module);
    $installer->installObjectModel('StripeIdempotencyKey');
    $installer->installObjectModel('StripePayment');

    $sql = 'ALTER TABLE `'._DB_PREFIX_.'stripe_official_processlogger` MODIFY msg TEXT';
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    $indexes = array(
        'id_idempotency_key',
        'id_cart',
        'idempotency_key',
        'id_payment_intent'
    );
    $already_indexed = array();
    $results = Db::getInstance()->executeS('SHOW INDEX FROM '._DB_PREFIX_.'stripe_idempotency_key');

    foreach ($results as $result) {
        array_push($already_indexed, $result['Column_name']);
    }

    $to_index = array_diff($indexes, $already_indexed);

    if (!empty($to_index)) {
        $sql = '';
        foreach ($to_index as $index) {
            $sql .= 'ALTER TABLE `'._DB_PREFIX_.'stripe_idempotency_key` ADD INDEX( `'.$index.'`);';
        }

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    }

    return true;
}
