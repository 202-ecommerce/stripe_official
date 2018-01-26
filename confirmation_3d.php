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

$useSSL = true;
if (!file_exists(dirname(__FILE__).'/../../config/config.inc.php')
    || !file_exists(dirname(__FILE__).'/../../init.php')
) {
    exit;
}
require dirname(__FILE__).'/../../config/config.inc.php';
require dirname(__FILE__).'/../../init.php';

$stripe = Module::getInstanceByName('stripe_official');
$lang_iso_code = Context::getContext()->language->iso_code;

if ($lang_iso_code == "fr") {
    print_r("L'authentification 3D-Secure a réussi. Cette fenêtre va bientôt se fermer.");
} elseif ($lang_iso_code == "it") {
    print_r("Il processo di verifica è ora completo. Questa finestra si chiuderà a breve.");
} elseif ($lang_iso_code == "de") {
    print_r("Der Verifizierungsprozess ist nun vollendet. Dieses Fenster wird in Kürze geschlossen.");
} elseif ($lang_iso_code == "es") {
    print_r("El proceso de verificación ha sido completado. Esta ventana se cerrará en breve.");
} else {
    print_r("The verification process is now complete. This window will close shortly.");
}
