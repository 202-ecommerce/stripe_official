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

use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialWebhookModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {
        $secret_key = $this->module->getSecretKey();

        ProcessLoggerHandler::logInfo($secret_key, null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();

        try {
            \Stripe\Stripe::setApiKey($secret_key);
            ProcessLoggerHandler::logInfo('setApiKey ok', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logInfo('setApiKey not ok', null, null, 'webhook');
            ProcessLoggerHandler::logError($e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            exit;
        }
        // Retrieve the request's body and parse it as JSON
        ProcessLoggerHandler::logInfo('Retrieve the request\'s body and parse it as JSON', null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();
        $input = @Tools::file_get_contents("php://input");
        ProcessLoggerHandler::logInfo('$input => ' . $input, null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();
        $event_json = json_decode($input);
        ProcessLoggerHandler::logInfo('$event_json->type => ' . $event_json->type, null, null, 'webhook');
        ProcessLoggerHandler::closeLogger();

        try {
            \Stripe\Event::retrieve($event_json->id);
            ProcessLoggerHandler::logInfo('event ' . $event_json->id . ' retrieved', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logError($e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            exit;
        }


        if (!$event_json) {
            $msg = 'JSON not valid';
            ProcessLoggerHandler::logError($msg, null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            echo $msg;
            exit;
        }
        // if ($event_json->data->object->metadata->verification_url != Configuration::get('PS_SHOP_DOMAIN')) {
        //     $msg = 'This order have been done on other site';
        //     ProcessLoggerHandler::logError($msg, null, null, 'webhook');
        //     ProcessLoggerHandler::closeLogger();
        //     http_response_code(500);
        //     echo $msg;
        //     exit;
        // }

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
        ProcessLoggerHandler::closeLogger();

        // Create the handler
        $handler = new ActionsHandler();

         // Set input data
        $handler->setConveyor(array(
                    'event_json' => $event_json,
                    'module' => $this->module,
                    'context' => $this->context,
                ));

        $handler->addActions('chargeWebhook');
        // Process actions chain
        if ($handler->process('ValidationOrder')) {
            // Retrieve and use resulting data
            $returnValues = $handler->getConveyor();
        } else {
            // Handle error
            ProcessLoggerHandler::logError('Order webhook process failed.', null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            exit;
        }
        echo 'OK';
        exit;
    }
}
