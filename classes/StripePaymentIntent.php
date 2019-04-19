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

class StripePaymentIntent extends ObjectModel
{
    /** @var string */
    public $id_payment_intent;
    /** @var string */
    public $status;
    /** @var float */
    public $amount;
    /** @var string */
    public $currency;
    /** @var date */
    public $date_add;
    /** @var date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_payment_intent',
        'primary'      => 'id_stripe_payment_intent',
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
            'amount' => array(
                'type'     => ObjectModel::TYPE_FLOAT,
                'validate' => 'isFloat',
                'size' => 10,
                'scale' => 2
            ),
            'currency'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 3,
            ),
            'date_add'  => array(
                'type'     => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'date_upd'  => array(
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

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setAmount($amount)
    {
        $module = Module::getInstanceByName('stripe_official');
        $amount = $module->isZeroDecimalCurrency(Tools::strtoupper($this->currency)) ? $amount : $amount / 100;

        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function setDateUpd($date_upd)
    {
        $this->date_upd = $date_upd;
    }

    public function getDateUpd()
    {
        return $this->date_upd;
    }

    public function findByIdPaymentIntent($idPaymentIntent)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(self::$definition['table']);
        $query->where('id_payment_intent = "'. pSQL($idPaymentIntent) .'"');

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if (!$data) {
            return false;
        }
        $this->hydrate($data);

        return $this;
    }
}
