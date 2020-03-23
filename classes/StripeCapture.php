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

class StripeCapture extends ObjectModel
{
    /** @var string */
    public $id_payment_intent;
    /** @var int */
    public $id_order;
    /** @var bool */
    public $expired;
    /** @var date */
    public $date_catch;
    /** @var date */
    public $date_authorize;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_capture',
        'primary'      => 'id_stripe_capture',
        'fields'       => array(
            'id_payment_intent'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 40,
            ),
            'id_order' => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size' => 10,
            ),
            'expired' => array(
                'type'     => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'date_catch'  => array(
                'type'     => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'date_authorize'  => array(
                'type'     => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
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

    public function setIdOrder($id_order)
    {
        $this->id_order = $id_order;
    }

    public function getIdOrder()
    {
        return $this->id_order;
    }

    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    public function getExpired()
    {
        return $this->expired;
    }

    public function setDateCatch($date_catch)
    {
        $this->date_catch = $date_catch;
    }

    public function getDateCatch()
    {
        return $this->date_catch;
    }

    public function setDateAuthorize($date_authorize)
    {
        $this->date_authorize = $date_authorize;
    }

    public function getDateAuthorize()
    {
        return $this->date_authorize;
    }

    public function getByIdPaymentIntent($id_payment_intent)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_payment_intent = "' . pSQL($id_payment_intent) . '"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }
}
