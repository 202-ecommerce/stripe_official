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

class StripeIdempotencyKey extends ObjectModel
{
    /** @var int */
    public $id_cart;
    /** @var string */
    public $idempotency_key;
    /** @var string */
    public $id_payment_intent;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_idempotency_key',
        'primary'      => 'id_idempotency_key',
        'fields'       => array(
            'id_cart'  => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size'     => 10,
            ),
            'idempotency_key'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
            'id_payment_intent'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
        ),
    );

    public function setIdCart($id_cart)
    {
        $this->id_cart = $id_cart;
    }

    public function getIdCart()
    {
        return $this->id_cart;
    }

    public function setIdempotencyKey($idempotency_key)
    {
        $this->idempotency_key = $idempotency_key;
    }

    public function getIdempotencyKey()
    {
        return $this->idempotency_key;
    }

    public function setIdPaymentIntent($id_payment_intent)
    {
        $this->id_payment_intent = $id_payment_intent;
    }

    public function getIdPaymentIntent()
    {
        return $this->id_payment_intent;
    }

    public function getByIdCart($id_cart)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_cart = '.pSQL($id_cart));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function getByIdPaymentIntent($id_payment_intent)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_payment_intent = "'.pSQL($id_payment_intent).'"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function createNewOne($id_cart, $datasIntent)
    {
        $idempotency_key = $id_cart.'_'.uniqid();

        $intent = \Stripe\PaymentIntent::create(
            $datasIntent,
            [
              'idempotency_key' => $idempotency_key
            ]
        );

        $stripeIdempotencyKey = new StripeIdempotencyKey();
        $stripeIdempotencyKey->getByIdCart($id_cart);
        $stripeIdempotencyKey->id_cart = $id_cart;
        $stripeIdempotencyKey->idempotency_key = $idempotency_key;
        $stripeIdempotencyKey->id_payment_intent = $intent->id;
        $stripeIdempotencyKey->save();

        $paymentIntent = new StripePaymentIntent();
        $paymentIntent->setIdPaymentIntent($intent->id);
        $paymentIntent->setStatus($intent->status);
        $paymentIntent->setAmount($intent->amount);
        $paymentIntent->setCurrency($intent->currency);
        $paymentIntent->setDateAdd(date("Y-m-d H:i:s", $intent->created));
        $paymentIntent->setDateUpd(date("Y-m-d H:i:s", $intent->created));
        $paymentIntent->save(false, false);

        return $intent;
    }
}
