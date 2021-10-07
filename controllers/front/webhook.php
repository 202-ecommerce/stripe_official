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

use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialWebhookModuleFrontController extends ModuleFrontController
{
    /**
     * Override displayMaintenancePage to prevent the maintenance page to be displayed
     *
     * @see FrontController::displayMaintenancePage()
     */
    protected function displayMaintenancePage()
    {
        return;
    }

    /**
     * Override displayRestrictedCountryPage to prevent page country is not allowed
     *
     * @see FrontController::displayRestrictedCountryPage()
     */
    protected function displayRestrictedCountryPage()
    {
        return;
    }

    /**
     * Override geolocationManagement to prevent country GEOIP blocking
     *
     * @see FrontController::geolocationManagement()
     *
     * @param Country $defaultCountry
     *
     * @return false
     */
    protected function geolocationManagement($defaultCountry)
    {
        return false;
    }

    /**
     * Override sslRedirection to prevent redirection
     *
     * @see FrontController::sslRedirection()
     */
    protected function sslRedirection()
    {
        return;
    }

    /**
     * Override canonicalRedirection to prevent redirection
     *
     * @see FrontController::canonicalRedirection()
     *
     * @param string $canonical_url
     */
    protected function canonicalRedirection($canonical_url = '')
    {
        return;
    }

    public function postProcess()
    {
        // Retrieve secret API key
        $secret_key = $this->module->getSecretKey();

        // Check API key validity
        $this->checkApiKey($secret_key);

        // Retrieve payload
        $input = @Tools::file_get_contents("php://input");
        ProcessLoggerHandler::logInfo(
            '$input => ' . $input,
            null,
            null,
            'webhook - postProcess'
        );

        // Retrieve http signature
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        ProcessLoggerHandler::logInfo(
            'set http stripe signature => '.$sig_header,
            null,
            null,
            'webhook - postProcess'
        );

        // Retrieve secret endpoint
        $endpoint_secret = Configuration::get(Stripe_official::WEBHOOK_SIGNATURE,null, Stripe_official::getShopGroupIdContext(), Stripe_official::getShopIdContext());
        ProcessLoggerHandler::logInfo(
            'set endpoint secret => '.$endpoint_secret,
            null,
            null,
            'webhook - postProcess'
        );

        // Construct event charge
        $event = $this->constructEvent($input, $sig_header, $endpoint_secret);

        // Retrieve payment intent
        if ($event->type == 'payment_intent.requires_action') {
            $paymentIntent = $event->data->object->id;
        } else {
            $paymentIntent = $event->data->object->payment_intent;
        }
        ProcessLoggerHandler::logInfo(
            'payment_intent : '.$paymentIntent,
            null,
            null,
            'webhook - postProcess'
        );

        // Create the handler
        $handler = $this->createWebhookHandler($event, $paymentIntent);

        // Handle actions
        $this->handleWebhookActions($handler, $event);

        ProcessLoggerHandler::closeLogger();
        exit;
    }

    private function checkApiKey($secretKey)
    {
        try {
            ProcessLoggerHandler::logInfo(
                $secretKey,
                null,
                null,
                'webhook - checkApiKey'
            );

            Stripe::setApiKey($secretKey);

            // Retrieve the request's body and parse it as JSON
            ProcessLoggerHandler::logInfo(
                'setApiKey ok. Retrieve the request\'s body and parse it as JSON',
                null,
                null,
                'webhook - checkApiKey'
            );
        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logError(
                'setApiKey not ok: ' . $e->getMessage(),
                null,
                null,
                'webhook - checkApiKey'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            exit;
        }
    }

    private function constructEvent($payload, $sigHeader, $secret)
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );

            if (!$event) {
                $msg = 'JSON not valid';
                ProcessLoggerHandler::logError(
                    $msg,
                    null,
                    null,
                    'webhook - constructEvent'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(500);
                echo $msg;
                exit;
            }

            if (!in_array($event->type, Stripe_official::$webhook_events)) {
                $msg = 'webhook "'.$event->type.'" call not yet supported';
                ProcessLoggerHandler::logInfo(
                    $msg,
                    null,
                    null,
                    'webhook - constructEvent'
                );
                ProcessLoggerHandler::closeLogger();
                echo $msg;
                exit;
            }

            ProcessLoggerHandler::logInfo(
                '$event => ' . $event,
                null,
                null,
                'webhook - constructEvent'
            );
            ProcessLoggerHandler::logInfo(
                'event ' . $event->id . ' retrieved',
                null,
                null,
                'webhook - constructEvent'
            );
            ProcessLoggerHandler::logInfo(
                'event type : ' . $event->type,
                null,
                null,
                'webhook - constructEvent'
            );
            return $event;
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            ProcessLoggerHandler::logError(
                'Invalid payload : '.$e->getMessage(),
                null,
                null,
                'webhook - constructEvent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            echo $e->getMessage();
            exit();
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            ProcessLoggerHandler::logError(
                'Invalid signature : '.$e->getMessage(),
                null,
                null,
                'webhook - constructEvent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            echo $e->getMessage();
            exit();
        }
    }

    private function createWebhookHandler($event, $paymentIntent)
    {
        ProcessLoggerHandler::logInfo(
            'creating webhook handler',
            null,
            null,
            'webhook - createWebhookHandler'
        );

        $events_states = array(
            'charge.expired' => Configuration::get('PS_OS_CANCELED'),
            'charge.failed' => Configuration::get('PS_OS_ERROR'),
            'charge.succeeded' => Configuration::get('PS_OS_PAYMENT'),
            'charge.captured' => Configuration::get('PS_OS_PAYMENT'),
            'charge.refunded' => Configuration::get('PS_OS_REFUND'),
            'charge.dispute.created' => Configuration::get(Stripe_official::SEPA_DISPUTE)
        );

        $handler = new ActionsHandler();
        $handler->setConveyor(array(
            'event_json' => $event,
            'module' => $this->module,
            'context' => $this->context,
            'events_states' => $events_states,
            'paymentIntent' => $paymentIntent,
        ));

        return $handler;
    }

    private function handleWebhookActions($handler, $event)
    {
        ProcessLoggerHandler::logInfo(
            'Starting webhook actions',
            null,
            null,
            'webhook - handleWebhookActions'
        );

        $eventType = $event->type;
        $paymentMethodType = $event->data->object->payment_method_details->type;

        if (($eventType == 'charge.succeeded'
                && $paymentMethodType == 'card')
            || ($eventType == 'charge.pending'
                && $paymentMethodType == 'sepa_debit')
            || ($eventType == 'payment_intent.requires_action'
                && $event->data->object->payment_method_types[0] == 'oxxo')) {
            ProcessLoggerHandler::logInfo(
                'Payment method flow without redirection',
                null,
                null,
                'webhook - handleWebhookActions'
            );
            $handler->addActions(
                'prepareFlowNone',
                'updatePaymentIntent',
                'createOrder',
                'sendMail',
                'saveCard',
                'addTentative'
            );
        } elseif (($eventType == 'charge.succeeded'
                && $paymentMethodType != 'sofort'
                && Stripe_official::$paymentMethods[$paymentMethodType]['flow'] == 'redirect')
            || ($eventType == 'charge.pending'
                && $paymentMethodType == 'sofort')) {
            ProcessLoggerHandler::logInfo(
                'Payment method flow with redirection',
                null,
                null,
                'webhook - handleWebhookActions'
            );
            $handler->addActions(
                'prepareFlowRedirectPaymentIntent',
                'updatePaymentIntent',
                'createOrder',
                'sendMail',
                'saveCard',
                'addTentative'
            );
        } else {
            $handler->addActions('chargeWebhook');
        }

        // Process actions chain
        if (!$handler->process('ValidationOrder')) {
            // Handle error
            ProcessLoggerHandler::logError(
                'Webhook actions process failed.',
                null,
                null,
                'webhook - handleWebhookActions'
            );
            ProcessLoggerHandler::closeLogger();
        }
    }
}
