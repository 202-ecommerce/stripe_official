<?php

use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

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

class stripe_officialOrderSuccessModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Configuration::get("STRIPE_RECIPE_MODE"))
            sleep(3);

        $payment_intent = Tools::getValue('payment_intent');
        $payment_method = Tools::getValue('payment_method');

        if ($this->registerStripeEvent($payment_intent)) {
            $handler = new ActionsHandler();
            $handler->setConveyor(array(
                //'event_json' => $event,
                'module' => $this->module,
                'context' => $this->context,
                //'events_states' => $events_states,
                'paymentIntent' => $payment_intent,
            ));

            if ($payment_method == 'card'
                || $payment_method == 'sepa_debit'
                || $payment_method == 'oxxo') {
                ProcessLoggerHandler::logInfo(
                    'Payment method flow without redirection',
                    null,
                    null,
                    'orderSuccess - initContent'
                );
                $handler->addActions(
                    'prepareFlowNone',
                    'updatePaymentIntent',
                    'createOrder',
                    'sendMail',
                    'saveCard',
                    'addTentative'
                );
            } elseif ($payment_method == 'sofort') {
                ProcessLoggerHandler::logInfo(
                    'Payment method flow with redirection',
                    null,
                    null,
                    'orderSuccess - initContent'
                );
                $handler->addActions(
                    'prepareFlowRedirectPaymentIntent',
                    'updatePaymentIntent',
                    'createOrder',
                    'sendMail',
                    'saveCard',
                    'addTentative'
                );
            }

            if (!$handler->process('ValidationOrderActions')) {
                // Handle error
                ProcessLoggerHandler::logError(
                    'Order creation process failed.',
                    null,
                    null,
                    'orderSuccess - initContent'
                );
                ProcessLoggerHandler::closeLogger();

                $url = Context::getContext()->link->getModuleLink(
                    'stripe_official',
                    'orderFailure',
                    array(),
                    true
                );
            } else {
                $url = $this->createOrder();
            }
        } else {
            $url = $this->createOrder();
        }

        Tools::redirect($url);
        exit;
    }

    private function registerStripeEvent($paymentIntent)
    {
        $intent = \Stripe\PaymentIntent::retrieve($paymentIntent);

        $eventCharge = isset($intent->charges->data[0]) ? $intent->charges->data[0] : $intent;

        $transitionStatus = [
            StripeEvent::CREATED_STATUS => [null],
            StripeEvent::FAILED_STATUS => [null],
            StripeEvent::PENDING_STATUS => [StripeEvent::CREATED_STATUS],
            StripeEvent::AUTHORIZED_STATUS => [StripeEvent::CREATED_STATUS, StripeEvent::PENDING_STATUS],
            StripeEvent::CAPTURED_STATUS => [StripeEvent::AUTHORIZED_STATUS],
            StripeEvent::REFUNDED_STATUS => [StripeEvent::CAPTURED_STATUS],
            StripeEvent::EXPIRED_STATUS => [StripeEvent::PENDING_STATUS],
        ];

        $lastRegisteredEvent = new StripeEvent();
        $lastRegisteredEvent = $lastRegisteredEvent->getLastRegisteredEventByPaymentIntent($paymentIntent);

        if ($lastRegisteredEvent->date_add != null) {
            $lastRegisteredEventDate = new DateTime($lastRegisteredEvent->date_add);
            $currentEventDate = new DateTime();
            $currentEventDate = $currentEventDate->setTimestamp($eventCharge->created);
            if ($lastRegisteredEventDate > $currentEventDate) {
                ProcessLoggerHandler::logInfo(
                    'This charge event come too late to be processed [Last event : ' . $lastRegisteredEventDate->format('Y-m-d H:m:s') . ' | Current event : ' . $currentEventDate->format('Y-m-d H:m:s') . '].',
                    null,
                    null,
                    'orderSuccess - registerStripeEvent'
                );
                ProcessLoggerHandler::closeLogger();
                return false;
            }
        }

        switch ($eventCharge ->type) {
            case 'charge.succeeded':
                $stripeEventStatus = StripeEvent::AUTHORIZED_STATUS;
                break;
            case 'charge.captured':
                $stripeEventStatus = StripeEvent::CAPTURED_STATUS;
                break;
            case 'charge.refunded':
                $stripeEventStatus = StripeEvent::REFUNDED_STATUS;
                break;
            case 'charge.failed':
                $stripeEventStatus = StripeEvent::FAILED_STATUS;
                break;
            case 'charge.expired':
                $stripeEventStatus = StripeEvent::EXPIRED_STATUS;
                break;
            case 'charge.pending':
            default:
                $stripeEventStatus = StripeEvent::PENDING_STATUS;
                break;
        }

        if (!in_array($lastRegisteredEvent->status, $transitionStatus[$stripeEventStatus]) ) {
            ProcessLoggerHandler::logInfo(
                'This Stripe module event "' .$stripeEventStatus.'" has already been processed.',
                'StripeEvent',
                $lastRegisteredEvent->id,
                'webhook - checkEventStatus'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }

        $stripeEventDate = new DateTime();
        $stripeEventDate = $stripeEventDate->setTimestamp($eventCharge->created);

        $stripeEvent = new StripeEvent();
        $stripeEvent = $stripeEvent->getEventByPaymentIntentNStatus($paymentIntent, $stripeEventStatus);
        $stripeEvent->setIdPaymentIntent($paymentIntent);
        $stripeEvent->setStatus($stripeEventStatus);
        $stripeEvent->setDateAdd($stripeEventDate->format('Y-m-d H:i:s'));
        $stripeEvent->setIsProcessed(true);

        try {
            return $stripeEvent->save();
        } catch (PrestaShopException $e) {
            ProcessLoggerHandler::logInfo(
                'Cannot process event',
                null,
                null,
                'orderSuccess - registerStripeEvent'
            );
            ProcessLoggerHandler::closeLogger();
            return false;
        }
    }

    private function createOrder()
    {
        ProcessLoggerHandler::logInfo(
            'Create order',
            null,
            null,
            'orderSuccess - createOrder'
        );
        ProcessLoggerHandler::closeLogger();

        $id_order = Order::getOrderByCartId($this->context->cart->id);

        if (isset($this->context->customer->secure_key)) {
            $secure_key = $this->context->customer->secure_key;
        } else {
            $secure_key = false;
        }

        return Context::getContext()->link->getPageLink(
            'order-confirmation',
            true,
            null,
            array(
                'id_cart' => (int)$this->context->cart->id,
                'id_module' => (int)$this->module->id,
                'id_order' => (int)$id_order,
                'key' => $secure_key
            )
        );
    }
}
