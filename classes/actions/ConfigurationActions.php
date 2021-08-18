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

use Stripe_officialClasslib\Actions\DefaultActions;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Stripe_officialClasslib\Registry;

class ConfigurationActions extends DefaultActions
{
    protected $context;
    protected $module;

    /*
        Input : 'source', 'response', 'context', 'module'
        Output : 'token', 'id_payment_intent', 'status', 'chargeId', 'amount'
     */
    public function registerKeys()
    {
        $this->module = $this->conveyor['module'];
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        $mode = Configuration::get(Stripe_official::MODE,null, $shopGroupId, $shopId);
        $secretKeyLive = Configuration::get(Stripe_official::KEY,null, $shopGroupId, $shopId);
        $secretKeyTest = Configuration::get(Stripe_official::TEST_KEY,null, $shopGroupId, $shopId);
        $webhookId = Configuration::get(Stripe_official::WEBHOOK_ID,null, $shopGroupId, $shopId);

        /* If mode has changed delete webhook of previous mode */
        if (Tools::getValue(Stripe_official::MODE) != $mode && $webhookId
            && (($secretKeyTest && $mode) || ($secretKeyLive && !$mode))) {
            $secretKey = $mode ? $secretKeyTest : $secretKeyLive;
            $stripeClient = new \Stripe\StripeClient($secretKey);
            $stripeClient->webhookEndpoints->delete($webhookId);
            Configuration::updateValue(Stripe_official::WEBHOOK_SIGNATURE, '', false, $shopGroupId, $shopId);
        }

        if (Tools::getValue(Stripe_official::MODE) == 1) {
            $secret_key = trim(Tools::getValue(Stripe_official::TEST_KEY));
            $publishable_key = trim(Tools::getValue(Stripe_official::TEST_PUBLISHABLE));

            if (!empty($secret_key) && !empty($publishable_key)) {
                if (strpos($secret_key, '_test_') !== false && strpos($publishable_key, '_test_') !== false) {
                    if ($this->module->checkApiConnection($secret_key)) {
                        Configuration::updateValue(Stripe_official::TEST_KEY, $secret_key, false, $shopGroupId, $shopId);
                        Configuration::updateValue(Stripe_official::TEST_PUBLISHABLE, $publishable_key, false, $shopGroupId, $shopId);
                    }
                } else {
                    $this->module->errors[] = $this->module->l('Live API keys provided instead of test API keys');
                }
            } else {
                $this->module->errors[] = $this->module->l('Client ID and Secret Key fields are mandatory');
            }

            Configuration::updateValue(Stripe_official::MODE, Tools::getValue(Stripe_official::MODE), false, $shopGroupId, $shopId);
        } else {
            $secret_key = trim(Tools::getValue(Stripe_official::KEY));
            $publishable_key = trim(Tools::getValue(Stripe_official::PUBLISHABLE));

            if (!empty($secret_key) && !empty($publishable_key)) {
                if (strpos($secret_key, '_live_') !== false && strpos($publishable_key, '_live_') !== false) {
                    if ($this->module->checkApiConnection($secret_key)) {
                        Configuration::updateValue(Stripe_official::KEY, $secret_key, false, $shopGroupId, $shopId);
                        Configuration::updateValue(Stripe_official::PUBLISHABLE, $publishable_key, false, $shopGroupId, $shopId);
                    }
                } else {
                    $this->module->errors['keys'] = $this->module->l('Test API keys provided instead of live API keys');
                }
            } else {
                $this->module->errors[] = $this->module->l('Client ID and Secret Key fields are mandatory');
            }

            Configuration::updateValue(Stripe_official::MODE, Tools::getValue(Stripe_official::MODE), false, $shopGroupId, $shopId);
        }

        return true;
    }

    /*
        Input : 'source', 'response', 'context', 'module'
        Output : 'token', 'id_payment_intent', 'status', 'chargeId', 'amount'
     */
    public function registerCatchAndAuthorize()
    {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();

        if (!Tools::getValue('catchandauthorize')) {
            Configuration::updateValue(Stripe_official::CATCHANDAUTHORIZE, null, false, $shopGroupId, $shopId);
        } elseif (Tools::getValue('catchandauthorize')
            && Tools::getValue('order_status_select') != ''
            && Tools::getValue('capture_expired') != '0') {
            Configuration::updateValue(Stripe_official::CAPTURE_EXPIRE, Tools::getValue('capture_expired'), false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::CAPTURE_STATUS, Tools::getValue('order_status_select'), false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::CATCHANDAUTHORIZE, Tools::getValue('catchandauthorize'), false, $shopGroupId, $shopId);
        } else {
            $this->module->errors[] = $this->module->l('Enable separate authorization and capture.');
        }

        return true;
    }

    /*
        Input : 'id_payment_intent', 'status'
        Output :
     */
    public function registerSaveCard()
    {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        if (!Tools::getValue('save_card')) {
            Configuration::updateValue(Stripe_official::SAVE_CARD, null,false, $shopGroupId, $shopId);
        } else {
            Configuration::updateValue(Stripe_official::SAVE_CARD, Tools::getValue('save_card'), false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::ASK_CUSTOMER, Tools::getValue('ask_customer'), false, $shopGroupId, $shopId);
        }

        return true;
    }

    /*
        Input : 'status', 'id_payment_intent', 'token', 'paymentIntent'
        Output : 'source', 'secure_key', 'result'
    */
    public function registerOtherConfigurations()
    {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        Configuration::updateValue(Stripe_official::ENABLE_IDEAL, Tools::getValue('ideal'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_SOFORT, Tools::getValue('sofort'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_GIROPAY, Tools::getValue('giropay'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_BANCONTACT, Tools::getValue('bancontact'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_FPX, Tools::getValue('fpx'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_EPS, Tools::getValue('eps'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_P24, Tools::getValue('p24'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_SEPA, Tools::getValue('sepa_debit'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_ALIPAY, Tools::getValue('alipay'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_OXXO, Tools::getValue('oxxo'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_APPLEPAY_GOOGLEPAY, Tools::getValue('applepay_googlepay'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::POSTCODE, Tools::getValue('postcode'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::CARDHOLDERNAME, Tools::getValue('cardholdername'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::REINSURANCE, Tools::getValue('reinsurance'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::VISA, Tools::getValue('visa'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::MASTERCARD, Tools::getValue('mastercard'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::AMERICAN_EXPRESS, Tools::getValue('american_express'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::CB, Tools::getValue('cb'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::DINERS_CLUB, Tools::getValue('diners_club'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::UNION_PAY, Tools::getValue('union_pay'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::JCB, Tools::getValue('jcb'), false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::DISCOVERS, Tools::getValue('discovers'), false, $shopGroupId, $shopId);

        if (!count($this->module->errors)) {
            $this->module->success = $this->module->l('Settings updated successfully.');
        }

        return true;
    }

    public function registerApplePayDomain()
    {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        if (Configuration::get(Stripe_official::KEY, null, $shopGroupId, $shopId) && Configuration::get(Stripe_official::KEY, null, $shopGroupId, $shopId) != '') {
            $this->module->addAppleDomainAssociation(Configuration::get(Stripe_official::KEY, null, $shopGroupId, $shopId));
        }

        return true;
    }

    public function registerWebhookSignature()
    {
        $this->context = $this->conveyor['context'];
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        $webhookSignature = Configuration::get(Stripe_official::WEBHOOK_SIGNATURE,null, $shopGroupId, $shopId);
        $webhookId = Configuration::get(Stripe_official::WEBHOOK_ID,null, $shopGroupId, $shopId);
        $webhookConfError = false;

        /* Check if webhook_id and webhook_signature have been defined */
        if (!$webhookSignature || $webhookSignature == '' || !$webhookId || $webhookId == '') {
            $webhookConfError = true;
        } else {
            /* Check if webhook access is write */
            try {
                $webhookEndpoint = \Stripe\WebhookEndpoint::retrieve($webhookId);
                $webhookUrlExpected = $this->context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Stripe_official::getShopIdContext() ?: Configuration::get('PS_SHOP_DEFAULT'));
                $webhookUpdateData = [];

                /* Check if webhook configuration is wrong */
                if ($webhookEndpoint->url != $webhookUrlExpected) {
                    $webhookUpdateData['url'] = $webhookUrlExpected;
                }
                /* Check if webhook events are wrong */
                $eventError = false;
                if (count($webhookEndpoint->enabled_events) == count(Stripe_official::$webhook_events)) {
                    foreach ($webhookEndpoint->enabled_events as $webhookEvent) {
                        if (!in_array($webhookEvent, Stripe_official::$webhook_events)) {
                            $eventError = true;
                        }
                    }
                } else {
                    $eventError = true;
                }
                if ($eventError)
                    $webhookUpdateData['enabled_events'] = Stripe_official::$webhook_events;

                if (!empty($webhookUpdateData)) {
                    $secretKey = Configuration::get(Stripe_official::MODE,null, $shopGroupId, $shopId) ? Configuration::get(Stripe_official::TEST_KEY,null, $shopGroupId, $shopId) : Configuration::get(Stripe_official::KEY,null, $shopGroupId, $shopId);
                    $stripeClient = new \Stripe\StripeClient($secretKey);
                    $stripeClient->webhookEndpoints->update($webhookId, $webhookUpdateData);
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $webhookConfError = true;
            }
        }

        if ($webhookConfError && StripeWebhook::countWebhooksList() < 16) {
            $webhooksList = StripeWebhook::getWebhookList();

            foreach ($webhooksList as $webhookEndpoint) {
                if ($webhookEndpoint->url == $this->context->link->getModuleLink('stripe_official', 'webhook', array(), true, Configuration::get('PS_LANG_DEFAULT'), Stripe_official::getShopIdContext() ?: Configuration::get('PS_SHOP_DEFAULT'))) {
                    $webhookEndpoint->delete();
                }
            }

            StripeWebhook::create();
        }

        return true;
    }
}
