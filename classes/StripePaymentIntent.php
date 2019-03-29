<?php

class StripePaymentIntent extends ObjectModel
{
    /** @var string */
    protected $id_payment_intent;
    /** @var string */
    protected $status;
    /** @var float */
    protected $amount;
    /** @var string */
    protected $currency;
    /** @var date */
    protected $date_add;
    /** @var date */
    protected $date_upd;

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
                'required' => true,
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

    public function __construct($id_stripe_payment_intent = null, $id_payment_intent = null, $status = null, $amount = null, $currency = null, $date_add = null, $date_upd = null)
    {
        parent::__construct();

        if ($id_stripe_payment_intent != null) {
            $this->id = $id_stripe_payment_intent;
            $this->id_payment_intent = $id_payment_intent;
            $this->status = $status;
            $this->amount = $amount;
            $this->currency = $currency;
            $this->date_add = $date_add;
            $this->date_upd = $date_upd;
        }
    }

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

    public static function getDatasByIdPaymentIntent($idPaymentIntent)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(self::$definition['table']);
        $query->where('id_payment_intent = "'. pSQL($idPaymentIntent) .'"');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query->build());
    }
}