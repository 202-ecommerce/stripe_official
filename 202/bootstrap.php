<?php
/**
 * 2007-2022 PrestaShop
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
ini_set('memory_limit', '200M');

define('TOT_SHARED_DIR', getenv('TOT_SHARED_DIR'));

$loader = require TOT_SHARED_DIR . '/vendor/autoload.php';

$basedir = '/var/www/html/';
require_once $basedir . 'config/config.inc.php';

//session_start();
if (defined('_PS_ADMIN_DIR_')) {
    $context = \Context::getContext();
    $context->employee = new \Employee(1);
    Cache::store('isLoggedBack1', true);

    require_once $basedir . 'bb/init.php';
} else {
    require_once $basedir . 'init.php';
}
