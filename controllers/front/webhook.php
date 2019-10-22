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

use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialWebhookModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {
        $secret_key = $this->module->getSecretKey();

        ProcessLoggerHandler::logInfo($secret_key, null, null, 'webhook');

        try {
            \Stripe\Stripe::setApiKey($secret_key);
        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logError('setApiKey not ok: ' . $e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            exit;
        }
        // Retrieve the request's body and parse it as JSON
        ProcessLoggerHandler::logInfo(
            'setApiKey ok. Retrieve the request\'s body and parse it as JSON',
            null,
            null,
            'webhook'
        );
        $input = @Tools::file_get_contents("php://input");
        ProcessLoggerHandler::logInfo('$input => ' . $input, null, null, 'webhook');
        $event_json = json_decode($input);
        ProcessLoggerHandler::logInfo('$event_json->type => ' . $event_json->type, null, null, 'webhook');

        try {
            \Stripe\Event::retrieve($event_json->id);
        } catch (Exception $e) {
            ProcessLoggerHandler::logError($e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
        ProcessLoggerHandler::logInfo('event ' . $event_json->id . ' retrieved', null, null, 'webhook');

        if (!$event_json) {
            $msg = 'JSON not valid';
            ProcessLoggerHandler::logError($msg, null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            echo $msg;
            exit;
        }

        http_response_code(200);
        $availlableType = array('charge.canceled', 'charge.failed', 'charge.succeeded', 'charge.pending');
        if (!in_array($event_json->type, $availlableType)) {
            $msg = 'webhook "'.$event_json->type.'" call not yet supported';
            ProcessLoggerHandler::logInfo($msg, null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            echo $msg;
            exit;
        }

        ProcessLoggerHandler::logInfo('starting webhook actions', null, null, 'webhook');

        // Create the handler
        $handler = new ActionsHandler();
        $handler->setConveyor(array(
                    'event_json' => $event_json,
                    'module' => $this->module,
                    'context' => $this->context,
                ));

        $handler->addActions('chargeWebhook');
        // Process actions chain
        if (!$handler->process('ValidationOrder')) {
            // Handle error
            ProcessLoggerHandler::logError('Order webhook process failed.', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            exit;
        }
        ProcessLoggerHandler::closeLogger();
        echo 'OK';
        exit;
    }
}
