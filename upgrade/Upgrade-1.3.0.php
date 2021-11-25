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

function upgrade_module_1_3_0()
{
    $alter_stripe_payment_table = true;
    $result = Db::getInstance()->executeS('SHOW FIELDS FROM '._DB_PREFIX_.'stripe_payment');

    if (!empty($result)) {
        foreach ($result as $res) {
            if ($res['Field'] == 'id_account') {
                $alter_stripe_payment_table = false;
            }
        }

        if ($alter_stripe_payment_table === true) {
            $sql = 'ALTER TABLE '._DB_PREFIX_.'stripe_payment ADD state tinyint(4) NOT NULL';
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
    }

    return true;
}
