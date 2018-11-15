<?php

ini_set('memory_limit', '200M');

define('TOT_SHARED_DIR', getenv('TOT_SHARED_DIR'));

$loader = require TOT_SHARED_DIR.'/vendor/autoload.php';

$basedir = '/var/www/html/';
require_once($basedir.'config/config.inc.php');

//session_start();
if (defined('_PS_ADMIN_DIR_')) {
$context = \Context::getContext();
$context->employee = new \Employee(1);
Cache::store('isLoggedBack1', true);

require_once($basedir.'bb/init.php');
} else {

require_once($basedir.'init.php');
}
