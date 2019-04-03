<?php
/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/classes/StripeLogger.php';
require_once dirname(__FILE__) . '/classes/StripePaymentRequestHandler.php';
require_once dirname(__FILE__) . '/classes/StripePayment.php';
require_once dirname(__FILE__) . '/classes/StripePaymentIntent.php';
require_once dirname(__FILE__) . '/classes/exceptions/StripePaymentRequestException.php';

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

// Set this as comment so Classlib will import the files; but don't uncomment !
// Installation will fail on PS 1.6 if "use" statements are in the main module
// file
//
// use Stripe_officialClasslib\Install\Installer;

class Stripe_official extends PaymentModule
{
    /**
     * List of objectModel used in this Module
     * @var array
     */
    public $objectModels = array(
        'StripePayment',
        'StripePaymentIntent',
    );

    // Read the Stripe guide: https://stripe.com/payments/payment-methods-guide
    public static $paymentMethods = array(
        'card' => array(
          'name' => 'Card', 'flow' => 'none'
        ),
        // 'alipay' => array(
        //   'name' => 'Alipay', 'flow' => 'redirect',
        //   'countries' => array('CN', 'HK', 'SG', 'JP'),
        //   'currencies' => array('aud', 'cad', 'eur', 'gbp', 'hkd', 'jpy', 'nzd', 'sgd', 'usd')
        // ),
        'bancontact' => array(
          'name' => 'Bancontact', 'flow' => 'redirect',
          'countries' => array('BE'),
          'currencies' => array('eur')
        ),
        // 'eps' => array(
        //   'name' => 'EPS', 'flow' => 'redirect',
        //   'countries' => array('AT'),
        //   'currencies' => array('eur')
        // ),
        'giropay' => array(
          'name' => 'Giropay', 'flow' => 'redirect',
          'countries' => array('DE'),
          'currencies' => array('eur'),
        ),
        'ideal' => array(
          'name' => 'iDEAL', 'flow' => 'redirect',
          'countries' => array('NL'),
          'currencies' => array('eur')
        ),
        // 'multibanco' => array(
        //   'name' => 'Multibanco', 'flow' => 'receiver',
        //   'countries' => array('PT'),
        //   'currencies' => array('eur'),
        // ),
        /* In BETA on request only. TODO: check what to do about this one
        'sepa_debit' => array(
          'name' => 'SEPA Direct Debit', 'flow' => 'none',
          'countries' => array('FR', 'DE', 'ES', 'BE', 'NL', 'LU', 'IT', 'PT', 'AT', 'IE', 'FI'),
          'currencies' => array('eur')
        ), */
        'sofort' => array(
          'name' => 'SOFORT', 'flow' => 'redirect',
          'countries' => array('DE', 'AT'),
          'currencies' => array('eur')
        ),
        // 'wechat' => array(
        //   'name' => 'WeChat', 'flow' => 'none',
        //   'countries' => array('CN', 'HK', 'SG', 'JP'),
        //   'currencies' => array('aud', 'cad', 'eur', 'gbp', 'hkd', 'jpy', 'nzd', 'sgd', 'usd'),
        // )
    );

    /* tab section shape */
    private $section_shape = 1;

    /* refund */
    public $refund = 0;

    public $errors = array();

    public $warning = array();

    public $success;

    public function __construct()
    {
        $this->name = 'stripe_official';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = '202 ecommerce';
        $this->bootstrap = true;
        $this->display = 'view';
        $this->module_key = 'bb21cb93bbac29159ef3af00bca52354';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->currencies = true;
        /* curl check */
        if (is_callable('curl_init') === false) {
            $this->errors[] = $this->l('To be able to use this module, please activate cURL (PHP extension).');
        }

        parent::__construct();

        $this->meta_title = $this->l('Stripe');
        $this->displayName = $this->l('Stripe payment module');
        $this->description = $this->l('Start accepting stripe payments today, directly from your shop!');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);

        /* Use a specific name to bypass an Order confirmation controller check */
        if (in_array(Tools::getValue('controller'), array('orderconfirmation', 'order-confirmation'))) {
            $this->displayName = $this->l('Payment by Stripe');
        }

        \Stripe\Stripe::setApiKey($this->getSecretKey());
        \Stripe\Stripe::setAppInfo("StripePrestashop_"._PS_VERSION_, $this->version, Configuration::get('PS_SHOP_DOMAIN_SSL'));
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Stripe_officialClasslib\Install\Installer();
        $installer->install($this);

        $partial_refund_state = Configuration::get('STRIPE_PARTIAL_REFUND_STATE');

        /* Create Order State for Stripe */
        if ($partial_refund_state === false) {
            $order_state = new OrderState();
            $langs = Language::getLanguages();
            foreach ($langs as $lang) {
                $order_state->name[$lang['id_lang']] = pSQL('Stripe Partial Refund');
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->logable = true;
            $order_state->color = '#FFDD99';
            $order_state->save();

            Configuration::updateValue('STRIPE_PARTIAL_REFUND_STATE', $order_state->id);
        }


        if (!Configuration::updateValue('STRIPE_MODE', 1)
            || !Configuration::updateValue('STRIPE_REFUND_MODE', 1)
            || !Configuration::updateValue('STRIPE_MINIMUM_AMOUNT_3DS', 50)
            || !Configuration::updateValue('STRIPE_ENABLE_IDEAL', 0)
            || !Configuration::updateValue('STRIPE_ENABLE_SOFORT', 0)
            || !Configuration::updateValue('STRIPE_ENABLE_GIROPAY', 0)
            || !Configuration::updateValue('STRIPE_ENABLE_BANCONTACT', 0)) {
                 return false;
        }

        if (!$this->registerHook('header')
            || !$this->registerHook('orderConfirmation')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('displayAdminOrderTabOrder')
            || !$this->registerHook('displayAdminOrderContentOrder')
            || !$this->registerHook('displayAdminCartsView')
            || !$this->registerHook('paymentOptions')
            || !$this->registerHook('adminOrder')) {
            return false;
        }

        if (!$this->installOrderState()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $installer = new Stripe_officialClasslib\Install\Installer();

        $installer->setModule($this);
        $result = parent::uninstall();
        $result &= $installer->uninstallModuleAdminControllers();
        $result &= $installer->uninstallConfiguration();

        return $result
            && Configuration::deleteByName('STRIPE_KEY')
            && Configuration::deleteByName('STRIPE_TEST_KEY')
            && Configuration::deleteByName('STRIPE_PUBLISHABLE')
            && Configuration::deleteByName('STRIPE_TEST_PUBLISHABLE')
            && Configuration::deleteByName('STRIPE_PARTIAL_REFUND_STATE')
            && Configuration::deleteByName('STRIPE_OS_SOFORT_WAITING')
            && Configuration::deleteByName('STRIPE_MODE')
            && Configuration::deleteByName('STRIPE_REFUND_MODE')
            && Configuration::deleteByName('STRIPE_ENABLE_IDEAL')
            && Configuration::deleteByName('STRIPE_ENABLE_SOFORT')
            && Configuration::deleteByName('STRIPE_ENABLE_GIROPAY')
            && Configuration::deleteByName('STRIPE_ENABLE_BANCONTACT');
    }

    /**
     * Create order state
     * @return boolean
     */
    public function installOrderState()
    {
        if (!Configuration::get('STRIPE_OS_SOFORT_WAITING')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('STRIPE_OS_SOFORT_WAITING')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'En attente de paiement Sofort';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting for Sofort payment';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'stripe_official/views/img/cc-sofort.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('STRIPE_OS_SOFORT_WAITING', (int) $order_state->id);
        }
        return true;
    }

    /*
     ** @Method: getContent
     ** @description: render main content
     **
     ** @arg:
     ** @return: (none)
     */
    public function getContent()
    {
        /* Check if SSL is enabled */
        if (!Configuration::get('PS_SSL_ENABLED')) {
            $this->warning[] = $this->l('You must enable SSL on the store if you want to use this module in production.');
        }

        /* Do Log In  */
        if (Tools::isSubmit('submit_login')) {
            if (Tools::getValue('STRIPE_MODE') == 1) {
                $secret_key = trim(Tools::getValue('STRIPE_TEST_KEY'));
                $publishable_key = trim(Tools::getValue('STRIPE_TEST_PUBLISHABLE'));

                if (!empty($secret_key) && !empty($publishable_key)) {
                    if (strpos($secret_key, 'test') !== false && strpos($publishable_key, 'test') !== false) {
                        if ($this->checkApiConnection($secret_key)) {
                            Configuration::updateValue('STRIPE_TEST_KEY', $secret_key);
                            Configuration::updateValue('STRIPE_TEST_PUBLISHABLE', $publishable_key);
                        }
                    } else {
                        $this->errors[] = $this->l('mode test with API key live');
                    }
                } else {
                    $this->errors[] = $this->l('Client ID and Secret Key fields are mandatory');
                }

                Configuration::updateValue('STRIPE_MODE', Tools::getValue('STRIPE_MODE'));
            } else {
                $secret_key = trim(Tools::getValue('STRIPE_KEY'));
                $publishable_key = trim(Tools::getValue('STRIPE_PUBLISHABLE'));

                if (!empty($secret_key) && !empty($publishable_key)) {
                    if (strpos($secret_key, 'live') !== false && strpos($publishable_key, 'live') !== false) {
                        if ($this->checkApiConnection($secret_key)) {
                            Configuration::updateValue('STRIPE_KEY', $secret_key);
                            Configuration::updateValue('STRIPE_PUBLISHABLE', $publishable_key);
                        }
                    } else {
                        $this->errors['keys'] = $this->l('mode live with API key test');
                    }
                } else {
                    $this->errors[] = $this->l('Client ID and Secret Key fields are mandatory');
                }

                Configuration::updateValue('STRIPE_MODE', Tools::getValue('STRIPE_MODE'));
            }

            if (!count($this->errors)) {
                $this->success = $this->l('Data succesfuly saved.');
            }

            Configuration::updateValue('STRIPE_ENABLE_IDEAL', Tools::getValue('ideal'));
            Configuration::updateValue('STRIPE_ENABLE_SOFORT', Tools::getValue('sofort'));
            Configuration::updateValue('STRIPE_ENABLE_GIROPAY', Tools::getValue('giropay'));
            Configuration::updateValue('STRIPE_ENABLE_BANCONTACT', Tools::getValue('bancontact'));
            Configuration::updateValue('STRIPE_ENABLE_APPLEPAY_GOOGLEPAY', Tools::getValue('applepay_googlepay'));
        }

        if (!Configuration::get('STRIPE_KEY') && !Configuration::get('STRIPE_PUBLISHABLE')
            && !Configuration::get('STRIPE_TEST_KEY') && !Configuration::get('STRIPE_TEST_PUBLISHABLE')) {
            $this->errors[] = $this->l('Keys are empty.');
        }

        /* Do Refund */
        if (Tools::isSubmit('submit_refund_id')) {
            $refund_id = Tools::getValue('STRIPE_REFUND_ID');
            if (!empty($refund_id)) {
                $refund = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            } else {
                $this->errors[] = $this->l('Please make sure to put a Stripe Id');
                return false;
            }

            if ($refund) {
                $this->refund = 1;
                Configuration::updateValue('STRIPE_REFUND_ID', Tools::getValue('STRIPE_REFUND_ID'));
            } else {
                $this->refund = 0;
                $this->errors[] = $this->l('This Stipe ID doesn\'t exist, please check it again');
                Configuration::updateValue('STRIPE_REFUND_ID', '');
            }

            $amount = null;
            $mode = Tools::getValue('STRIPE_REFUND_MODE');
            if ($mode == 0) {
                $amount = Tools::getValue('STRIPE_REFUND_AMOUNT');
            }

            $this->apiRefund($refund[0]['id_stripe'], $refund[0]['currency'], $mode, $refund[0]['id_cart'], $amount);

            if (!count($this->errors)) {
                $this->success = $this->l('Data succesfuly saved.');
            }
        }

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $domain = Tools::getShopDomainSsl(true, true);
        } else {
            $domain = Tools::getShopDomain(true, true);
        }

        $this->context->controller->addJS($this->_path.'/views/js/faq.js');
        $this->context->controller->addJS($this->_path.'/views/js/back.js');
        $this->context->controller->addJS($this->_path.'/views/js/PSTabs.js');
        $this->context->controller->addCSS($this->_path.'/views/css/started.css');
        $this->context->controller->addCSS($this->_path.'/views/css/tabs.css');

        if ((Configuration::get('STRIPE_TEST_KEY') != '' && Configuration::get('STRIPE_TEST_PUBLISHABLE') != '')
            || (Configuration::get('STRIPE_KEY') != '' && Configuration::get('STRIPE_PUBLISHABLE') != '')) {
            $keys_configured = true;
        } else {
            $keys_configured = false;
        }

        $this->context->smarty->assign(array(
            'logo' => $domain.__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'/views/img/Stripe_logo.png',
            'new_base_dir', $this->_path,
            'keys_configured' => $keys_configured,
            'link' => new Link(),
        ));

        $this->displaySomething();
        $this->assignSmartyVars();
        $this->displayRefundForm();

        if (count($this->warning)) {
            $this->context->smarty->assign('warnings', $this->displayWarning($this->warning));
        }

        if (!empty($this->success) && !count($this->errors)) {
            $this->context->smarty->assign('success', $this->displayConfirmation($this->success));
        }

        if (count($this->errors)) {
            $this->context->smarty->assign('errors', $this->displayError($this->errors));
        }

        return $this->display($this->_path, 'views/templates/admin/main.tpl');
    }

    /*
     ** Display Form
     */
    protected function assignSmartyVars()
    {
        $this->context->smarty->assign(array(
            'stripe_mode' => Configuration::get('STRIPE_MODE'),
            'stripe_key' => Configuration::get('STRIPE_KEY'),
            'stripe_publishable' => Configuration::get('STRIPE_PUBLISHABLE'),
            'stripe_test_publishable' => Configuration::get('STRIPE_TEST_PUBLISHABLE'),
            'stripe_test_key' => Configuration::get('STRIPE_TEST_KEY'),
            'ideal' => Configuration::get('STRIPE_ENABLE_IDEAL'),
            'sofort' => Configuration::get('STRIPE_ENABLE_SOFORT'),
            'giropay' => Configuration::get('STRIPE_ENABLE_GIROPAY'),
            'bancontact' => Configuration::get('STRIPE_ENABLE_BANCONTACT'),
            'applepay_googlepay' => Configuration::get('STRIPE_ENABLE_APPLEPAY_GOOGLEPAY'),
            'googlepay' => Configuration::get('STRIPE_ENABLE_GOOGLEPAY'),
            'product_payment' => Configuration::get('STRIPE_PRODUCT_PAYMENT'),
            'url_webhhoks' => $this->context->link->getModuleLink($this->name, 'webhook', array(), true),
        ));
    }

    /*
     ** Display Submit form for Refund
     */
    public function displayRefundForm()
    {
        $fields_form = array();
        $fields_value = array();

        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Choose an Order you want to Refund'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Stripe Payment ID'),
                    'desc' => '<i>'.$this->l('To process a refund, please input Stripe’s payment ID below, which can be found in the « Payments » tab of this plugin').'</i>',
                    'name' => 'STRIPE_REFUND_ID',
                    'class' => 'fixed-width-xxl',
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'desc' => '<i>'.$this->l('We’ll submit any refund you make to your customer’s bank immediately.').'<br>'.
                        $this->l('Your customer will then receive the funds from a refund approximately 2-3 business days after the date on which the refund was initiated.').'<br>'.
                        $this->l('Refunds take 5 to 10 days to appear on your cutomer’s statement.').'</i>',
                    'name' => 'STRIPE_REFUND_MODE',
                    'size' => 50,
                    'values' => array(
                        array(
                            'id' => 'active_on_refund',
                            'value' => 1,
                            'label' => $this->l('Full refund')
                        ),
                        array(
                            'id' => 'active_off_refund',
                            'value' => 0,
                            'label' => $this->l('Partial Refund')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Amount'),
                    'desc' => $this->l('Please, enter an amount your want to refund'),
                    'name' => 'STRIPE_REFUND_AMOUNT',
                    'size' => 20,
                    'id' => 'refund_amount',
                    'class' => 'fixed-width-sm',
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Request Refund'),
                'class' => 'btn btn-default pull-right button',
            ),
        );
        $this->refund = 1;

        $submit_action = 'submit_refund_id';
        $fields_value = array(
            'STRIPE_REFUND_ID' => Configuration::get('STRIPE_REFUND_ID'),
            'STRIPE_REFUND_MODE' => Configuration::get('STRIPE_REFUND_MODE'),
            'STRIPE_REFUND_AMOUNT' => Configuration::get('STRIPE_REFUND_AMOUNT'),
        );

        $this->context->smarty->assign('refund_form', $this->renderGenericForm($fields_form, $fields_value, $this->getSectionShape(), $submit_action));
    }

    /*
     ** @Method: displaySomething
     ** @description: just display something (it's something)
     **
     ** @arg: (none)
     ** @return: (none)
     */
    public function displaySomething()
    {
        $return_url = '';

        if (Configuration::get('PS_SSL_ENABLED')) {
            $domain = Tools::getShopDomainSsl(true);
        } else {
            $domain = Tools::getShopDomain(true);
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $return_url = urlencode($domain.$_SERVER['REQUEST_URI'].$this->getSectionShape());
        }

        $this->context->smarty->assign('return_url', $return_url);
    }

    /*
     ** @Method: renderGenericForm
     ** @description: render generic form for prestashop
     **
     ** @arg: $fields_form, $fields_value, $submit = false, array $tpls_vars = array()
     ** @return: (none)
     */
    public function renderGenericForm($fields_form, $fields_value = array(), $fragment = false, $submit = false, array $tpl_vars = array())
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        if ($fragment !== false) {
            $helper->token .= '#'.$fragment;
        }

        if ($submit) {
            $helper->submit_action = $submit;
        }

        $helper->tpl_vars = array_merge(array(
            'fields_value' => $fields_value,
            'id_language' => $this->context->language->id,
            'back_url' => $this->context->link->getAdminLink('AdminModules')
            .'&configure='.$this->name
            .'&tab_module='.$this->tab
            .'&module_name='.$this->name.($fragment !== false ? '#'.$fragment : '')
        ), $tpl_vars);

        return $helper->generateForm($fields_form);
    }

    /*
     ** @Method: apiRefund
     ** @description: Make a Refund (charge) with Stripe
     **
     ** @arg: amount, id_stripe
     ** @amount: if null total refund
     ** @currency: "USD", "EUR", etc..
     ** @mode: (boolean) ? total : partial
     ** @return: (none)
     */
    public function apiRefund($refund_id, $currency, $mode, $id_card, $amount = null)
    {
        if ($this->checkApiConnection($secret_key) && !empty($refund_id)) {
            $refund = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            if ($mode == 1) { /* Total refund */
                try {
                    $ch = \Stripe\Charge::retrieve($refund_id);
                    $ch->refunds->create();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $this->errors[] = $e->getMessage();
                    return false;
                }

                Db::getInstance()->Execute(
                    'UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = 2, `date_add` = NOW(), `refund` = "'
                    .pSQL($refund[0]['amount']).'" WHERE `id_stripe` = "'.pSQL($refund_id).'"'
                );
            } else { /* Partial refund */
                if (!$this->isZeroDecimalCurrency($currency)) {
                    $ref_amount = $amount * 100;
                }
                try {
                    $ch = \Stripe\Charge::retrieve($refund_id);
                    $ch->refunds->create(array('amount' => $ref_amount));
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $this->errors[] = $e->getMessage();
                    return false;
                }

                $amount += ($refund[0]['refund']);
                if ($amount == $refund[0]['amount']) {
                    $result = 2;
                } else {
                    $result = 3;
                }

                if ($amount <= $refund[0]['amount']) {
                    Db::getInstance()->Execute(
                        'UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = '.(int)$result.', `date_add` = NOW(), `refund` = "'
                        .pSQL($amount).'" WHERE `id_stripe` = "'.pSQL($refund_id).'"'
                    );
                }
            }

            $id_order = Order::getOrderByCartId($id_card);
            $order = new Order($id_order);
            $state = Db::getInstance()->getValue('SELECT `result` FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');

            if ($state == 2) {
                /* Refund State */
                $order->setCurrentState(7);
            } elseif ($state == 3) {
                /* Partial Refund State */
                $order->setCurrentState(Configuration::get('STRIPE_PARTIAL_REFUND_STATE'));
            }
            $this->success = $this->l('Refunds processed successfully');
        } else {
            $this->errors[] = $this->l('Invalid Stripe credentials, please check your configuration.');
        }
    }

    public function isZeroDecimalCurrency($currency)
    {
        // @see: https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
        $zeroDecimalCurrencies = array(
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'UGX',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF'
        );
        return in_array($currency, $zeroDecimalCurrencies);
    }

    public function createOrder($paymentIntent, $params)
    {
        $message = 'Stripe Transaction ID: '.$paymentIntent->id;

        $source = \Stripe\Source::retrieve($params['token']);

        $secure_key = isset($this->context->customer->secure_key) ? $this->context->customer->secure_key : false;
        try {
            $paid = $this->isZeroDecimalCurrency($params['currency']) ? $params['amount'] : $params['amount'] / 100;
            /* Add transaction on Prestashop back Office (Order) */
            if (!empty($source->type) && $source->type == 'sofort' && $paymentIntent->status == 'pending') {
                $status = Configuration::get('STRIPE_OS_SOFORT_WAITING');
                $result = 4;
            } else {
                $status = Configuration::get('PS_OS_PAYMENT');
                $result = 1;
            }
            $this->validateOrder(
                (int)$params['cart_id'],
                (int)$status,
                $paid,
                $this->l('Payment by Stripe'),
                $message,
                array(),
                null,
                false,
                $secure_key
            );
        } catch (PrestaShopException $e) {
            $this->_error[] = (string)$e->getMessage();
        }

        /* Add transaction on database */
        $this->addTentative(
            $params['id_payment_intent'],
            $source->owner->name,
            $source->type,
            $source->amount,
            0,
            $source->currency,
            $result,
            (int)$params['cart_id']
        );

        $id_order = Order::getOrderByCartId($params['cart_id']);

        $url = Context::getContext()->link->getPageLink(
            'order-confirmation',
            true,
            null,
            array(
                'id_cart' => (int)$params['cart_id'],
                'id_module' => (int)$this->id,
                'id_order' => (int)$id_order,
                'key' => $secure_key
            )
        );

        /* Ajax redirection Order Confirmation */
        $response = array(
            'code' => '1',
            'url' => $url
        );

        return $response;
    }

    /*
     ** @Method: addTentative
     ** @description: Add Payment on Database
     **
     ** @return: (none)
     */
    protected function addTentative($id_payment_intent, $name, $type, $amount, $refund, $currency, $result, $id_cart = 0)
    {
        if ($id_cart == 0) {
            $id_cart = (int)$this->context->cart->id;
        }

        if ($type == 'American Express') {
            $type = 'amex';
        } elseif ($type == 'Diners Club') {
            $type = 'diners';
        }

        if (!$this->isZeroDecimalCurrency($currency)) {
            $amount /= 100;
            $refund /= 100;
        }

        $intent = \Stripe\PaymentIntent::retrieve($id_payment_intent);
        $charges = $intent->charges->data;

        $stripePayment = new StripePayment();
        // $stripePayment->setIdStripe($charges[0]->id);
        $stripePayment->setIdPaymentIntent($id_payment_intent);
        $stripePayment->setName($name);
        $stripePayment->setIdCart((int)$id_cart);
        // $stripePayment->setLast4((int)$charges[0]->payment_method_details->card->last4);
        $stripePayment->setType(Tools::strtolower($type));
        $stripePayment->setAmount($amount);
        $stripePayment->setRefund($refund);
        $stripePayment->setCurrency(Tools::strtolower($currency));
        $stripePayment->setResult((int)$result);
        $stripePayment->setState((int)Configuration::get('STRIPE_MODE'));
        $stripePayment->setDateAdd(date("Y-m-d H:i:s"));
        $stripePayment->save();

        $orderId = Order::getOrderByCartId($id_cart);
        $orderPaymentDatas = OrderPayment::getByOrderId($orderId);

        $orderPayment = new OrderPayment($orderPaymentDatas[0]->id);
        // $orderPayment->transaction_id = $charges[0]->id;
        $orderPayment->save();
    }

    /*
     ** @Method: getSectionShape
     ** @description: get section shape fragment
     **
     ** @arg:
     ** @return: (none)
     */
    protected function getSectionShape()
    {
        return 'stripe_step_'.(int)$this->section_shape++;
    }

    public function getSecretKey()
    {
        if (Configuration::get('STRIPE_MODE')) {
            return Configuration::get('STRIPE_TEST_KEY');
        } else {
            return Configuration::get('STRIPE_KEY');
        }
    }

    public function getPublishableKey()
    {
        if (Configuration::get('STRIPE_MODE')) {
            return Configuration::get('STRIPE_TEST_PUBLISHABLE');
        } else {
            return Configuration::get('STRIPE_PUBLISHABLE');
        }
    }

    protected function checkApiConnection($secretKey = null)
    {
        if (!$secretKey) {
            $secretKey = $this->getSecretKey();
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            \Stripe\Account::retrieve();
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->errors[] = $e->getMessage();
            return false;
        }
        return true;
    }

    public function updateConfigurationKey($oldKey, $newKey, $defaultValue)
    {
        if (Configuration::hasKey($oldKey)) {
            $set = '';

            if ($oldKey == '_PS_STRIPE_secure' && Configuration::get($oldKey) == '0') {
                $set = ',`value`=2';
            }

            $sql = "UPDATE `"._DB_PREFIX_."configuration`
                    SET `name`='".pSQL($newKey)."'".$set."
                    WHERE `name`='".pSQL($oldKey)."'";

            return Db::getInstance()->execute($sql);
        } else {
            Configuration::updateValue($newKey, $defaultValue);
            return true;
        }
    }

    /*
    ** Retrieve the current payment intent or create a new one
    */
    protected function retrievePaymentIntent($amount, $currency)
    {
        if (isset($this->context->cookie->stripe_payment_intent)) {
          try {
            $intent = \Stripe\PaymentIntent::retrieve($this->context->cookie->stripe_payment_intent);

            // Check that the amount is still correct
            if ($intent->amount != $amount) {
              $intent->update(["amount" => $amount]);
            }

            return $intent;
          } catch (Exception $e) {
            unset($this->context->cookie->stripe_payment_intent);
            error_log($e->getMessage());
          }
        }

        try {
          $intent = \Stripe\PaymentIntent::create([
            "amount" => $amount,
            "currency" => $currency,
            "payment_method_types" => [array_keys(self::$paymentMethods)],
          ]);

          // Keep the payment intent ID in session
          $this->context->cookie->stripe_payment_intent = $intent->id;

        $paymentIntent = new StripePaymentIntent();
        $paymentIntent->setIdPaymentIntent($intent->id);
        $paymentIntent->setStatus($intent->status);
        $paymentIntent->setAmount($intent->amount);
        $paymentIntent->setCurrency($intent->currency);
        $paymentIntent->setDateAdd(date("Y-m-d H:i:s", $intent->created));
        $paymentIntent->setDateUpd(date("Y-m-d H:i:s", $intent->created));
        $paymentIntent->save(false, false);

          return $intent;
        } catch (Exception $e) {
          error_log($e->getMessage());
          die($e->getMessage());
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (Tools::getIsset('controller') && Tools::getValue('controller') == 'AdminModules' && Tools::getIsset('configure') && Tools::getValue('configure') == $this->name) {
            Media::addJsDef(array(
                'transaction_refresh_url' => $this->context->link->getAdminLink('AdminAjaxTransaction', true, array(), array('ajax' => 1, 'action' => 'refresh')),
            ));
        }
    }

    public function hookDisplayAdminCartsView($params)
    {
        $stripePayment = new StripePayment();
        $paymentInformations = $stripePayment->getStripePaymentByCart($params['cart']->id);

        if (empty($paymentInformations->getIdStripe())) {
            return;
        }

        $paymentInformations->url_dashboard = $stripePayment->getDashboardUrl();

        $this->context->smarty->assign(array(
            'paymentInformations' => $paymentInformations
        ));

        return $this->display(__FILE__, 'views/templates/hook/admin_cart.tpl');
    }

    public function hookDisplayAdminOrderTabOrder()
    {
        return $this->display(__FILE__, 'views/templates/hook/admin_tab_order.tpl');
    }

    public function hookDisplayAdminOrderContentOrder($params)
    {
        $stripePayment = new StripePayment();
        $stripePayment->getStripePaymentByCart($params['order']->id_cart);
        $this->context->smarty->assign(array(
            'stripe_charge' => $stripePayment->getIdStripe(),
            'stripe_paymentIntent' => $stripePayment->getIdPaymentIntent(),
            'stripe_date' => $stripePayment->getDateAdd(),
            'stripe_dashboardUrl' => $stripePayment->getDashboardUrl()
        ));

        return $this->display(__FILE__, 'views/templates/hook/admin_content_order.tpl');
    }

    /**
     * Load JS
     */
    public function hookHeader()
    {
        if ($this->context->controller->php_self != 'order') {
            return;
        }

        $currency = $this->context->currency->iso_code;
        $address = new Address($this->context->cart->id_address_invoice);
        $amount = $this->context->cart->getOrderTotal();
        $amount = $this->isZeroDecimalCurrency($currency) ? $amount : $amount * 100;

        // The payment intent for this order
        $intent = $this->retrievePaymentIntent($amount, $currency);

        if (!$intent) {
            // Problem with the payment intent creation... TODO: log/alert
            return;
        }

        // Merchant country (for payment request API)
        $merchantCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));

        // Javacript variables needed by Elements
        Media::addJsDef(array(
          'stripe_pk' => $this->getPublishableKey(),
          'stripe_merchant_country_code' => $merchantCountry->iso_code,
          'stripe_payment_id' => $intent->id,
          'stripe_client_secret' => $intent->client_secret,

          'stripe_baseDir' => $this->context->link->getBaseLink($this->context->shop->id, true),
          'stripe_module_dir' => $this->_path,
          'stripe_verification_url' => Configuration::get('PS_SHOP_DOMAIN'),

          'stripe_currency' => strtolower($currency),
          'stripe_amount' => $amount,

          'stripe_firstname' => $this->context->customer->firstname,
          'stripe_lastname' => $this->context->customer->lastname,
          'stripe_fullname' => $this->context->customer->firstname . ' ' .
                               $this->context->customer->lastname,

          'stripe_address_line1' => $address->address1,
          'stripe_address_line2' => $address->address2,
          'stripe_address_city' => $address->city,
          'stripe_address_zip_code' => $address->postcode,
          'stripe_address_country' => $address->country,
          'stripe_address_country_code' => Country::getIsoById($address->id_country),

          'stripe_phone' => $address->phone_mobile ?: $address->phone,
          'stripe_email' => $this->context->customer->email,

          'stripe_locale' => $this->context->language->iso_code,

          'stripe_validation_return_url' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),

          'stripe_css' => '{
            "base": {
              "iconColor": "#666ee8",
              "color": "#31325f",
              "fontWeight": 400,
              "fontFamily": "-apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif",
              "fontSmoothing": "antialiased",
              "fontSize": "15px",
              "::placeholder": { "color": "#aab7c4" },
              ":-webkit-autofill": { "color": "#666ee8" }
            }
          }'
        ));

        $this->context->controller->registerJavascript($this->name.'-stripe-v3', 'https://js.stripe.com/v3/', array('server'=>'remote'));
        $this->context->controller->registerJavascript($this->name.'-payments', 'modules/'.$this->name.'/views/js/payments.js');

        if (Configuration::get('STRIPE_ENABLE_APPLEPAY_GOOGLEPAY')) {
            $this->context->controller->registerJavascript($this->name.'-stripepaymentrequest', 'modules/'.$this->name.'/views/js/payment_request.js');
        }

        $this->context->controller->registerStylesheet($this->name.'-checkoutcss', 'modules/'.$this->name.'/views/css/checkout.css');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        // Fetch country based on invoice address and currency
        $address = new Address($params['cart']->id_address_invoice);
        $country = Country::getIsoById($address->id_country);
        $currency = strtolower($this->context->currency->iso_code);

        // Show only the payment methods that are relevant to the selected country and currency
        $options = array();
        foreach (self::$paymentMethods as $name => $paymentMethod) {
            // Check for country support
            if (isset($paymentMethod['countries']) && !in_array($country, $paymentMethod['countries'])) {
                //continue;
            }

            // Check for currency support
            if (isset($paymentMethod['currencies']) && !in_array($currency, $paymentMethod['currencies'])) {
                //continue;
            }

            // The customer can potientially use this payment method
            $option = new PaymentOption();
            $option
            ->setModuleName($this->name)
            //->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/'.$cc_img))
            ->setCallToActionText($this->l('Pay by ' . $paymentMethod['name']));

            // Display additional information for redirect and receiver based payment methods
            if (in_array($paymentMethod['flow'], array('redirect', 'receiver'))) {
                $option->setAdditionalInformation(
                    $this->context->smarty->fetch('module:' . $this->name . '/views/templates/front/payment_info_' . $paymentMethod['flow'] . '.tpl')
                );
            }

            // Payment methods with embedded form fields
            $option->setForm($this->context->smarty->fetch('module:' . $this->name .
            '/views/templates/front/payment_form_' . $name . '.tpl'));

            $options[] = $option;
        }

        return $options;
    }

    /*
     ** Hook Order Confirmation
     */
    public function hookOrderConfirmation($params)
    {
        $this->context->smarty->assign('stripe_order_reference', pSQL($params['order']->reference));
        if ($params['order']->module == $this->name) {
            return $this->display(__FILE__, 'views/templates/front/order-confirmation.tpl');
        }
    }
}
