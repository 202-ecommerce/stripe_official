<?php

/**
 * 2007-2021 PrestaShop
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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @throws \Stripe\Exception\ApiErrorException
 */
function upgrade_module_2_3_6()
{
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'stripe_official_processlogger` MODIFY msg TEXT';
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();

        $mode = Configuration::get(Stripe_official::MODE);
        $key = Configuration::get(Stripe_official::KEY);
        $testKey = Configuration::get(Stripe_official::TEST_KEY);
        $publishable = Configuration::get(Stripe_official::PUBLISHABLE);
        $testPublishable = Configuration::get(Stripe_official::TEST_PUBLISHABLE);
        $webhookSignature = Configuration::get(Stripe_official::WEBHOOK_SIGNATURE);
        $webhookId = Configuration::get(Stripe_official::WEBHOOK_ID);
        $accountId = Configuration::get(Stripe_official::ACCOUNT_ID);

        $refundId = Configuration::get(Stripe_official::REFUND_ID);
        $refundMode = Configuration::get(Stripe_official::REFUND_MODE);
        $minimumAmount3ds = Configuration::get(Stripe_official::MINIMUM_AMOUNT_3DS);
        $partialRefundState = Configuration::get(Stripe_official::PARTIAL_REFUND_STATE);
        $refundAmount = Configuration::get(Stripe_official::REFUND_AMOUNT);

        $enableIdeal = Configuration::get(Stripe_official::ENABLE_IDEAL);
        $enableSofort = Configuration::get(Stripe_official::ENABLE_SOFORT);
        $enableGiropay = Configuration::get(Stripe_official::ENABLE_GIROPAY);
        $enableBancontact = Configuration::get(Stripe_official::ENABLE_BANCONTACT);
        $enableFpx = Configuration::get(Stripe_official::ENABLE_FPX);
        $enableEps = Configuration::get(Stripe_official::ENABLE_EPS);
        $enableP24 = Configuration::get(Stripe_official::ENABLE_P24);
        $enableSepa = Configuration::get(Stripe_official::ENABLE_SEPA);
        $enableAlipay = Configuration::get(Stripe_official::ENABLE_ALIPAY);
        $enableOxxo = Configuration::get(Stripe_official::ENABLE_OXXO);
        $enableAppleGooglePay = Configuration::get(Stripe_official::ENABLE_APPLEPAY_GOOGLEPAY);

        $captureWaiting = Configuration::get(Stripe_official::CAPTURE_WAITING);
        $osSofortWaiting = Configuration::get(Stripe_official::OS_SOFORT_WAITING);
        $oxxoWaiting = Configuration::get(Stripe_official::OXXO_WAITING);
        $sepaWaiting = Configuration::get(Stripe_official::SEPA_WAITING);
        $sepaDispute = Configuration::get(Stripe_official::SEPA_DISPUTE);

        $postCode = Configuration::get(Stripe_official::POSTCODE);
        $cardHolderName = Configuration::get(Stripe_official::CARDHOLDERNAME);
        $reinsurance = Configuration::get(Stripe_official::REINSURANCE);
        $visa = Configuration::get(Stripe_official::VISA);
        $masterCard = Configuration::get(Stripe_official::MASTERCARD);
        $americanExpress = Configuration::get(Stripe_official::AMERICAN_EXPRESS);
        $cb = Configuration::get(Stripe_official::CB);
        $dinersClub = Configuration::get(Stripe_official::DINERS_CLUB);
        $unionPay = Configuration::get(Stripe_official::UNION_PAY);
        $jcb = Configuration::get(Stripe_official::JCB);
        $discovers = Configuration::get(Stripe_official::DISCOVERS);

        $catchAndAuthorize = Configuration::get(Stripe_official::CATCHANDAUTHORIZE);
        $captureStatus = Configuration::get(Stripe_official::CAPTURE_STATUS);
        $captureExpire = Configuration::get(Stripe_official::CAPTURE_EXPIRE);
        $saveCard = Configuration::get(Stripe_official::SAVE_CARD);
        $askCustomer = Configuration::get(Stripe_official::ASK_CUSTOMER);

        Configuration::updateValue(Stripe_official::MODE, $mode, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::KEY, $key, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::PUBLISHABLE, $publishable, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::TEST_KEY, $testKey, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::TEST_PUBLISHABLE, $testPublishable, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::WEBHOOK_SIGNATURE, $webhookSignature, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::WEBHOOK_ID, $webhookId, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ACCOUNT_ID, $accountId, false, $shopGroupId, $shopId);

        Configuration::updateValue(Stripe_official::REFUND_ID, $refundId, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::REFUND_MODE, $refundMode, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::MINIMUM_AMOUNT_3DS, $minimumAmount3ds, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::PARTIAL_REFUND_STATE, $partialRefundState, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::REFUND_AMOUNT, $refundAmount, false, $shopGroupId, $shopId);

        Configuration::updateValue(Stripe_official::ENABLE_IDEAL, $enableIdeal, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_SOFORT, $enableSofort, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_GIROPAY, $enableGiropay, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_BANCONTACT, $enableBancontact, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_FPX, $enableFpx, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_EPS, $enableEps, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_P24, $enableP24, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_SEPA, $enableSepa, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_ALIPAY, $enableAlipay, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_OXXO, $enableOxxo, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::ENABLE_APPLEPAY_GOOGLEPAY, $enableAppleGooglePay, false, $shopGroupId, $shopId);

        Configuration::updateValue(Stripe_official::CAPTURE_WAITING, $captureWaiting, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::OS_SOFORT_WAITING, $osSofortWaiting, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::OXXO_WAITING, $oxxoWaiting, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::SEPA_WAITING, $sepaWaiting, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::SEPA_DISPUTE, $sepaDispute, false, $shopGroupId, $shopId);

        Configuration::updateValue(Stripe_official::POSTCODE, $postCode, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::CARDHOLDERNAME, $cardHolderName, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::REINSURANCE, $reinsurance, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::VISA, $visa, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::MASTERCARD, $masterCard, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::AMERICAN_EXPRESS, $americanExpress, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::CB, $cb, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::DINERS_CLUB, $dinersClub, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::UNION_PAY, $unionPay, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::JCB, $jcb, false, $shopGroupId, $shopId);
        Configuration::updateValue(Stripe_official::DISCOVERS, $discovers, false, $shopGroupId, $shopId);

        if (!$catchAndAuthorize) {
            Configuration::updateValue(Stripe_official::CATCHANDAUTHORIZE, null, false, $shopGroupId, $shopId);
        } elseif ($catchAndAuthorize && $captureStatus != '' && $captureExpire != '0') {
            Configuration::updateValue(Stripe_official::CAPTURE_EXPIRE, $captureExpire, false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::CAPTURE_STATUS, $captureStatus, false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::CATCHANDAUTHORIZE, $catchAndAuthorize, false, $shopGroupId, $shopId);
        }

        if (!$saveCard) {
            Configuration::updateValue(Stripe_official::SAVE_CARD, null, false, $shopGroupId, $shopId);
        } else {
            Configuration::updateValue(Stripe_official::SAVE_CARD, $saveCard, false, $shopGroupId, $shopId);
            Configuration::updateValue(Stripe_official::ASK_CUSTOMER, $askCustomer, false, $shopGroupId, $shopId);
        }
    }

    return true;
}
