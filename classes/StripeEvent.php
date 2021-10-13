<?php

use Stripe_officialClasslib\Database\Index\IndexType;

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

class StripeEvent extends ObjectModel
{
    const PENDING_STATUS = 'PENDING';
    const AUTHORIZED_STATUS = 'AUTHORIZED';
    const CAPTURED_STATUS = 'CAPTURED';
    const REFUNDED_STATUS = 'REFUNDED';
    const FAILED_STATUS = 'FAILED';
    const EXPIRED_STATUS = 'EXPIRED';

    /**
     * @var string $id_payment_intent
     */
    public $id_payment_intent;
    /**
     * @var string $status
     */
    public $status;
    /**
     * @var DateTime $date_add
     */
    public $date_add;
    /**
     * @var bool $is_processed
     */
    public $is_processed;

    /**
     * @var array $definition
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_event',
        'primary'      => 'id_stripe_event',
        'fields'       => array(
            'id_payment_intent'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 40,
            ),
            'status'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 30,
            ),
            'date_add'  => array(
                'type'     => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'is_processed' => array(
                'type'      => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            )
        ),
        'indexes'      => [
            [
                'fields'    => [
                    [
                        'column' => 'id_payment_intent',
                    ],
                    [
                        'column' => 'status',
                    ]
                ],
                'type' => IndexType::UNIQUE,
            ],
        ],
    );

    public function setIdPaymentIntent($id_payment_intent)
    {
        $this->id_payment_intent = $id_payment_intent;
    }

    public function getIdPaymentIntent()
    {
        return $this->id_payment_intent;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function isProcessed()
    {
        return $this->is_processed;
    }

    public function setIsProcessed($is_processed)
    {
        $this->is_processed = $is_processed;
    }

    public function getLastRegisteredEventByPaymentIntent($paymentIntent)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_payment_intent = "' . pSQL($paymentIntent) . '"');
        $query->orderBy('date_add DESC');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function getEventByPaymentIntentNStatus($paymentIntent, $status)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_payment_intent = "' . pSQL($paymentIntent) . '" AND status = "' . pSQL($status) . '"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }
}
