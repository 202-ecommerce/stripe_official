<?php
/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!file_exists(dirname(__FILE__).'/../../config/config.inc.php')
    || !file_exists(dirname(__FILE__).'/../../init.php')
) {
    die('ko');
}

require dirname(__FILE__).'/../../config/config.inc.php';
require dirname(__FILE__).'/../../init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

$stripe = Module::getInstanceByName('stripe_official');

if (Tools::getValue('token_stripe')) {
    /* Refresh Button Back Office on Transaction */
    if ($stripe && $stripe->active) {
        echo $stripe->displayTransaction(1, Tools::getValue('token_stripe'), Tools::getValue('id_employee'));
    }
}
