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

class StripeCustomer extends ObjectModel
{
    /** @var int */
    public $id_customer;
    /** @var string */
    public $stripe_customer_key;
    /** @var string */
    public $id_account;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_customer',
        'primary'      => 'id_stripe_customer',
        'fields'       => array(
            'id_customer' => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size' => 10,
            ),
            'stripe_customer_key'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 50,
            ),
            'id_account'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 50,
            ),
        ),
    );

    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
    }

    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    public function setStripeCustomerKey($stripe_customer_key)
    {
        $this->stripe_customer_key = $stripe_customer_key;
    }

    public function getStripeCustomerKey()
    {
        return $this->stripe_customer_key;
    }

    public function setIdAccount($id_account)
    {
        $this->id_account = $id_account;
    }

    public function getIdAccount()
    {
        return $this->id_account;
    }

    public function getCustomerById($id_customer, $id_account)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_customer = '.pSQL($id_customer));
        $query->where('id_account = "'.pSQL($id_account).'"');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function stripeCustomerExists($email, $stripe_customer_id)
    {
        $customersList = \Stripe\Customer::all(
            [
                'email' => $email,
                'limit' => 100
            ]
        );

        foreach ($customersList as $customer) {
            if ($customer['id'] == $stripe_customer_id) {
                return true;
            }
        }

        return false;
    }
}
