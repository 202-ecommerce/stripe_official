<?php

class StripePayment extends ObjectModel
{
    /** @var string */
    public $id_stripe;
    /** @var string */
    public $id_payment_intent;
    /** @var string */
    public $name;
    /** @var int */
    public $id_cart;
    /** @var int */
    public $last4;
    /** @var string */
    public $type;
    /** @var float */
    public $amount;
    /** @var float */
    public $refund;
    /** @var string */
    public $currency;
    /** @var int */
    public $result;
    /** @var int */
    public $state;
    /** @var date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_payment',
        'primary'      => 'id_payment',
        'fields'       => array(
            'id_stripe'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
            'id_payment_intent'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 255,
            ),
            'name'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 30,
            ),
            'id_cart' => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size' => 10,
            ),
            'last4'  => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size'     => 4,
            ),
            'type'  => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'size'     => 20,
            ),
            'amount' => array(
                'type'     => ObjectModel::TYPE_FLOAT,
                'validate' => 'isFloat',
                'size' => 10,
                'scale' => 2
            ),
            'refund' => array(
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
            'result'  => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size'     => 1,
            ),
            'state'  => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isInt',
                'size'     => 1,
            ),
            'date_add'  => array(
                'type'     => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
        ),
    );

    public function setIdStripe($id_stripe)
    {
        $this->id_stripe = $id_stripe;
    }

    public function getIdStripe()
    {
        return $this->id_stripe;
    }

    public function setIdPaymentIntent($id_payment_intent)
    {
        $this->id_payment_intent = $id_payment_intent;
    }

    public function getIdPaymentIntent()
    {
        return $this->id_payment_intent;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIdCart($id_cart)
    {
        $this->id_cart = $id_cart;
    }

    public function getIdCart()
    {
        return $this->id_cart;
    }

    public function setLast4($last4)
    {
        $this->last4 = $last4;
    }

    public function getLast4()
    {
        return $this->last4;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setRefund($refund)
    {
        $this->refund = $refund;
    }

    public function getRefund()
    {
        return $this->refund;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        //$paymentInformations['state'] = Tools::safeOutput($paymentInformations['state']) ? $this->l('Test') : $this->l('Live');

        return $this->state;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getStripePaymentByCart($id_cart)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_cart = ' . (int)$id_cart);

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
        if ($result == false) {
            return $this;
        }

        $this->hydrate($result);

        return $this;
    }

    public function getDashboardUrl()
    {
        $url_type = '';
        if ($this->state == 1) {
            $url_type = 'test';
        }

        switch ($this->result) {
            case 0:
                $this->result = 'n';
                break;
            case 1:
                $this->result = '';
                break;
            case 2:
                $this->result = 2;
                break;
            case 4:
                $this->result = 4;
                break;

            default:
                $this->result = 3;
                break;
        }

        $url_dashboard = array(
            'charge' => 'https://dashboard.stripe.com/'.$url_type.'/payments/'.$this->id_stripe,
            'paymentIntent' => 'https://dashboard.stripe.com/'.$url_type.'/payments/'.$this->id_payment_intent
        );

        return $url_dashboard;
    }
}
