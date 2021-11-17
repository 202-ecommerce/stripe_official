<?php

class AdminStripe_officialPaymentIntentController extends ModuleAdminController
{
    /** @var bool Active bootstrap for Prestashop 1.6 */
    public $bootstrap = true;

    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = StripeEvent::class;

    /** @var string Associated table name */
    public $table = 'stripe_event';

    /** @var string|false Object identifier inside the associated table */
    public $identifier = 'id_payment_intent';

    /** @var string Default ORDER BY clause when is not defined */
    protected $_defaultOrderBy = 'id_stripe_event';

    /** @var bool List content lines are clickable if true */
    protected $list_no_link = true;

    public $multishop_context = 1;

    protected $actions = ['details'];

    /**
     * @see AdminController::__construct()
     */
    public function __construct()
    {
        parent::__construct();

        $this->_select = 'o.id_order, sp.id_cart, sp.id_payment_intent, sp.type, spi.status, o.reference';
        $this->_join =
            'INNER JOIN `'._DB_PREFIX_.'stripe_payment` sp ON (a.id_payment_intent = sp.id_payment_intent AND sp.result > 0)
            INNER JOIN `'._DB_PREFIX_.'stripe_payment_intent` spi ON (sp.id_payment_intent = spi.id_payment_intent)
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (sp.id_cart = o.id_cart)';

        $this->explicitSelect = true;

        $this->fields_list = [
            'id_order' => [
                'title' => $this->module->l('Order ID', 'AdminStripe_officialPaymentIntentController'),
                'filter_key' => 'o!id_order',
                'orderby' => false,
            ],
            'id_cart' => [
                'title' => $this->module->l('Cart ID', 'AdminStripe_officialPaymentIntentController'),
                'filter_key' => 'sp!id_cart',
                'orderby' => false,
            ],
            'id_payment_intent' => [
                'title' => $this->module->l('Payment Intent', 'AdminStripe_officialPaymentIntentController'),
                'filter_key' => 'sp!id_payment_intent',
                'orderby' => false,
            ],
            'type' => [
                'title' => $this->module->l('Payment Method', 'AdminStripe_officialPaymentIntentController'),
                'orderby' => false,
            ],
            'status' => [
                'title' => $this->module->l('Charge Status', 'AdminStripe_officialPaymentIntentController'),
                'filter_key' => 'spi!status',
                'orderby' => false,
            ],
            'reference' => [
                'title' => $this->module->l('Order Reference', 'AdminStripe_officialPaymentIntentController'),
                'orderby' => false,
            ]
        ];
    }

    /**
     * @see AdminController::initToolbar()
     */
    public function initToolbar()
    {
        parent::initToolbar();
        // Remove the add new item button
        unset($this->toolbar_btn['new']);
        unset($this->toolbar_btn['delete']);
    }

    /**
     * @throws PrestaShopException
     * @see AdminController::initToolbar()
     */
    public function renderDetails()
    {
        $this->_select = null;
        $this->_join = null;
        $this->_group = null;
        $this->_filter = null;
        $this->_where = ' AND a.id_payment_intent = "' . Tools::getValue('id_payment_intent') . '"';
        $this->_orderBy = 'date_add';

        $this->actions = [];

        $this->list_simple_header = true;
        $this->explicitSelect = false;

        $this->fields_list = [
            'id_payment_intent' => [
                'title' => $this->module->l('Payment Intent', 'AdminStripe_officialPaymentIntentController'),
            ],
            'status' => [
                'title' => $this->module->l('Event Status', 'AdminStripe_officialPaymentIntentController'),
            ],
            'is_processed' => [
                'title' => $this->module->l('Processed', 'AdminStripe_officialPaymentIntentController'),
            ],
            'date_add' => [
                'title' => $this->module->l('Saving date', 'AdminStripe_officialPaymentIntentController'),
                'align' => 'right',
                'class' => 'fixed-width-xs',
            ],
            'flow_type' => [
                'title' => $this->module->l('Flow Type', 'AdminStripe_officialPaymentIntentController'),
            ],
        ];

        return $this->renderList();
    }
}
