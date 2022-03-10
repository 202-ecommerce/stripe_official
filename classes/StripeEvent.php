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

class StripeEvent extends ObjectModel
{
    const CREATED_STATUS = 'CREATED';
    const PENDING_STATUS = 'PENDING';
    const AUTHORIZED_STATUS = 'AUTHORIZED';
    const CAPTURED_STATUS = 'CAPTURED';
    const REFUNDED_STATUS = 'REFUNDED';
    const FAILED_STATUS = 'FAILED';
    const EXPIRED_STATUS = 'EXPIRED';
    const REQUIRES_ACTION_STATUS = 'REQUIRES_ACTION';

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
     * @var $flow_type
     */
    public $flow_type = 'webhook';

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
                'type'     => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'flow_type'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 30,
            ),
        ),
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

    public function setFlowType($flow_type)
    {
        $this->flow_type = $flow_type;
    }

    public function getFlowType()
    {
        return $this->flow_type;
    }

    public function save($null_values = false, $auto_date = false)
    {
        return parent::save($null_values, $auto_date);
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

    public static function getStatusAssociatedToChargeType($chargeType)
    {
        switch ($chargeType)
        {
            case 'charge.succeeded':
            case 'succeeded':
                return StripeEvent::AUTHORIZED_STATUS;

            case 'charge.captured':
            case 'captured':
                return StripeEvent::CAPTURED_STATUS;

            case 'charge.refunded':
            case 'refunded':
                return StripeEvent::REFUNDED_STATUS;

            case 'charge.failed':
            case 'failed':
                return StripeEvent::FAILED_STATUS;

            case 'charge.expired':
            case 'expired':
                return StripeEvent::EXPIRED_STATUS;

            case 'charge.pending':
            case 'pending':
                return StripeEvent::PENDING_STATUS;

            case 'payment_intent.requires_action':
            case 'requires_action':
                return StripeEvent::REQUIRES_ACTION_STATUS;

            default:
                return false;
        }
    }

    public static function getTransitionStatusByNewStatus($newStatus)
    {
        switch ($newStatus)
        {
            case StripeEvent::REQUIRES_ACTION_STATUS:
                return [
                    StripeEvent::CREATED_STATUS,
                    StripeEvent::FAILED_STATUS,
                ];

            case StripeEvent::PENDING_STATUS:
                return [
                    StripeEvent::CREATED_STATUS,
                    StripeEvent::REQUIRES_ACTION_STATUS,
                ];

            case StripeEvent::AUTHORIZED_STATUS:
            case StripeEvent::FAILED_STATUS:
            case StripeEvent::EXPIRED_STATUS:
                return [
                    StripeEvent::CREATED_STATUS,
                    StripeEvent::REQUIRES_ACTION_STATUS,
                    StripeEvent::PENDING_STATUS,
                    StripeEvent::FAILED_STATUS,
                ];

            case StripeEvent::CAPTURED_STATUS:
                return [
                    StripeEvent::AUTHORIZED_STATUS,
                ];

            case StripeEvent::REFUNDED_STATUS:
                return [
                    StripeEvent::AUTHORIZED_STATUS,
                    StripeEvent::CAPTURED_STATUS,
                ];

            case StripeEvent::CREATED_STATUS:
            default:
                return [];
        }
    }

    public static function validateTransitionStatus($currentStatus, $newStatus)
    {
        $transitionStatus = self::getTransitionStatusByNewStatus($newStatus);

        return in_array($currentStatus, $transitionStatus);
    }
}
