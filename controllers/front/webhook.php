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
        ProcessLoggerHandler::logInfo(
            '[ Webhook Process Beginning ]',
            null,
            null,
            'webhook - postProcess'
        );

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

        // Check if shop is the good one
        $cart = new Cart($event->data->object->metadata->id_cart);
        if ($cart->id_shop_group != Stripe_official::getShopGroupIdContext()
            || $cart->id_shop != Stripe_official::getShopIdContext()) {
            ProcessLoggerHandler::logInfo(
                $msg = 'This cart does not come from this shop',
                'Cart',
                $cart->id,
                'webhook - postProcess'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
            echo $msg;
            exit;
        }

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

        // Registry Stripe event in database
        $registeredEvent = $this->registerEvent($event, $paymentIntent);

        // Create the handler
        $handler = $this->createWebhookHandler($event, $paymentIntent);

        // Handle actions
        $this->handleWebhookActions($handler, $event);

        // Valid Stripe event process
        $this->validProcessEvent($registeredEvent);

        ProcessLoggerHandler::logInfo(
            '[ Webhook Process Ending ]',
            null,
            null,
            'webhook - postProcess'
        );
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

    private function registerEvent($event, $paymentIntent)
    {
        try {
            $stripeEventStatus = $this->checkEventStatus($event, $paymentIntent);
            $stripeEventDate = new DateTime();
            $stripeEventDate = $stripeEventDate->setTimestamp($event->created);

            $stripeEvent = new StripeEvent();
            $stripeEvent = $stripeEvent->getEventByPaymentIntentNStatus($paymentIntent, $stripeEventStatus);
            if ($stripeEvent->id != null) {
                $stripeEvent->setDateAdd($stripeEventDate->format('Y-m-d H:i:s'));
            } else {
                $stripeEvent->setIdPaymentIntent($paymentIntent);
                $stripeEvent->setStatus($stripeEventStatus);
                $stripeEvent->setDateAdd($stripeEventDate->format('Y-m-d H:i:s'));
                $stripeEvent->setIsProcessed(false);
            }

            if (!$stripeEvent->save()) {
                $msg = 'An issue appears during saving Stripe module event in database (the event probably already exists).';
                ProcessLoggerHandler::logInfo(
                    $msg,
                    null,
                    null,
                    'webhook - registerEvent'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(400);
                die($msg);
            }

            return $stripeEvent;
        } catch (PrestaShopException $e) {
            $msg = 'A problem appears while recording the Stripe module event => ' . $e->getMessage();
            ProcessLoggerHandler::logInfo(
                $msg,
                null,
                null,
                'webhook - registerEvent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die($msg);
        }
    }

    private function validProcessEvent($registeredEvent)
    {
        try {
            $registeredEvent->setIsProcessed(true);
            if (!$registeredEvent->save()) {
                $msg = 'An issue appears while updating the Stripe module event';
                ProcessLoggerHandler::logInfo(
                    $msg,
                    null,
                    null,
                    'webhook - validProcessEvent'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(400);
                die($msg);
            }
        } catch (PrestaShopException $e) {
            $msg = 'A problem appears while completing the Stripe event process => ' . $e->getMessage();
            ProcessLoggerHandler::logInfo(
                $msg,
                null,
                null,
                'webhook - validProcessEvent'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die($msg);
        }
    }

    private function checkEventStatus($event, $paymentIntent)
    {
        $eventStatus = StripeEvent::getStatusAssociatedToChargeType($event->type);

        if (!$eventStatus) {
            $msg = 'Charge event does not need to be processed : ' . $event->type;
            ProcessLoggerHandler::logInfo(
                $msg,
                null,
                null,
                'webhook - checkEventStatus'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
            die($msg);
        }

        $lastRegisteredEvent = new StripeEvent();
        $lastRegisteredEvent = $lastRegisteredEvent->getLastRegisteredEventByPaymentIntent($paymentIntent);

        if ($lastRegisteredEvent->id == null) {
            $msg = 'This payment intent doesn\'t exist. This charge event is perhaps intended for another webhook.';
            ProcessLoggerHandler::logInfo(
                $msg,
                null,
                null,
                'webhook - checkEventStatus'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
            die($msg);
        }

        if ($lastRegisteredEvent->status != $eventStatus && $lastRegisteredEvent->date_add != null) {
            $lastRegisteredEventDate = new DateTime($lastRegisteredEvent->date_add);
            $currentEventDate = new DateTime();
            $currentEventDate = $currentEventDate->setTimestamp($event->created);
            if ($lastRegisteredEventDate > $currentEventDate) {
                $msg = 'This charge event come too late to be processed [Last event : ' . $lastRegisteredEventDate->format('Y-m-d H:m:s') . ' | Current event : ' . $currentEventDate->format('Y-m-d H:m:s') . '].';
                ProcessLoggerHandler::logInfo(
                    $msg,
                    null,
                    null,
                    'webhook - checkEventStatus'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(200);
                die($msg);
            }
        }

        if ($lastRegisteredEvent->status == $eventStatus) {
            if ($lastRegisteredEvent->isProcessed()) {
                $msg = 'This Stripe module event "' . $eventStatus . '" has already been processed.';
                ProcessLoggerHandler::logInfo(
                    $msg,
                    'StripeEvent',
                    $lastRegisteredEvent->id,
                    'webhook - checkEventStatus'
                );
                ProcessLoggerHandler::closeLogger();
                http_response_code(200);
                echo $msg;
                exit;
            }
            ProcessLoggerHandler::logInfo(
                $eventStatus . ' event restarted because the previous one did not complete processing',
                'StripeEvent',
                $lastRegisteredEvent->id,
                'webhook - checkEventStatus'
            );
        } elseif (!StripeEvent::validateTransitionStatus($lastRegisteredEvent->status, $eventStatus) || !$lastRegisteredEvent->isProcessed()) {
            if ($eventStatus === StripeEvent::CAPTURED_STATUS) {
                if (isset($event->data->object->payment_method_details->type))
                    $paymentMethodType =  $event->data->object->payment_method_details->type;
                elseif (isset($event->data->object->payment_method_types[0]))
                    $paymentMethodType = $event->data->object->payment_method_types[0];
                else
                    $paymentMethodType = null;

                if ($paymentMethodType == 'card' && Configuration::get(Stripe_official::CATCHANDAUTHORIZE) != 'on') {
                    $msg = 'The card payment amount has already been captured.';
                    ProcessLoggerHandler::logInfo(
                        $msg,
                        null,
                        null,
                        'webhook - checkEventStatus'
                    );
                    ProcessLoggerHandler::closeLogger();
                    http_response_code(200);
                    die($msg);
                }
            }
            $msg = 'This Stripe module event "' . $eventStatus . '" cannot be processed because [Last event status: ' . $lastRegisteredEvent->status . ' | Processed : ' . ($lastRegisteredEvent->isProcessed() ? 'Yes' : 'No') . '].';
            ProcessLoggerHandler::logInfo(
                $msg,
                'StripeEvent',
                $lastRegisteredEvent->id,
                'webhook - checkEventStatus'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die($msg);
        }

        return $eventStatus;
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

        if (isset($event->data->object->payment_method_details->type))
            $paymentMethodType =  $event->data->object->payment_method_details->type;
        elseif (isset($event->data->object->payment_method_types[0]))
            $paymentMethodType = $event->data->object->payment_method_types[0];
        else
            $paymentMethodType = null;

        if (($eventType == 'charge.succeeded' && $paymentMethodType == 'card')
            || ($eventType == 'charge.pending' && $paymentMethodType == 'sepa_debit')
            || ($eventType == 'payment_intent.requires_action' && $paymentMethodType == 'oxxo')
        ) {
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
        } elseif (($eventType == 'charge.pending' && $paymentMethodType == 'sofort')
            || ($eventType == 'charge.succeeded'
                && Stripe_official::$paymentMethods[$paymentMethodType]['flow'] == 'redirect'
                && $paymentMethodType != 'sofort')
        ) {
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
        if (!$handler->process('ValidationOrderActions')) {
            // Handle error
            ProcessLoggerHandler::logError(
                'Webhook actions process failed.',
                null,
                null,
                'webhook - handleWebhookActions'
            );
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            die('Webhook actions process failed.');
        }
    }
}
