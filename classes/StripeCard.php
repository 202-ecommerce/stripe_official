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

use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class StripeCard extends ObjectModel
{
    /** @var int */
    public $id_customer;
    /** @var string */
    public $stripe_customer_key;
    /** @var string */
    public $stripe_card_key;
    /** @var string */
    public $payment_method;
    /** @var string */
    public $brand;
    /** @var string */
    public $expire;
    /** @var int */
    public $last4;

    public function __construct($stripe_customer_key = null, $stripe_card_key = null)
    {
        $this->stripe_customer_key = $stripe_customer_key;
        $this->stripe_card_key = $stripe_card_key;
    }

    public function save($null_values = false, $auto_date = true)
    {
        try {
            $payment_method = \Stripe\PaymentMethod::retrieve(
                $this->payment_method
            );

            $payment_method->attach([
                'customer' => $this->stripe_customer_key,
            ]);
        } catch (PrestaShopException $e) {
            $this->_error[] = (string)$e->getMessage();
            ProcessLoggerHandler::logError('Save card - '.(string)$e->getMessage(), null, null, 'StripeCard');
            return false;
        }

        return true;
    }

    public function delete()
    {
        try {
            $payment_method = \Stripe\PaymentMethod::retrieve(
                $this->payment_method
            );
            $payment_method->detach();
        } catch (PrestaShopException $e) {
            $this->_error[] = (string)$e->getMessage();
            ProcessLoggerHandler::logError('Delete card - '.(string)$e->getMessage(), null, null, 'StripeCard');
            return false;
        }

        return true;
    }

    public function getAllCustomerCards()
    {
        try {
            $allCards = \Stripe\PaymentMethod::all([
                'customer' => $this->stripe_customer_key,
                'type' => 'card',
            ]);
        } catch (Exception $e) {
            return array();
        }

        return $allCards->data;
    }
}
