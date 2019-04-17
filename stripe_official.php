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

/**
* Stripe object model
*/
require_once dirname(__FILE__) . '/classes/StripePayment.php';
require_once dirname(__FILE__) . '/classes/StripePaymentIntent.php';

/**
* Stripe object for ApplePay and GooglePay
*/
require_once dirname(__FILE__) . '/classes/StripePaymentRequestHandler.php';
require_once dirname(__FILE__) . '/classes/exceptions/StripePaymentRequestException.php';

// use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

/**
* Stripe official PrestaShop module main class extends payment class
* Please note this module use _202 PrestaShop Classlib Project_ (202 classlib) a library developped by "202 ecommerce"
* This library provide utils common features as DB installer, internal logger, chain of resposability design pattern
*
* To let module compatible with Prestashop 1.6 please keep this following line commented in PrestaShop 1.6:
* // use Stripe_officialClasslib\Install\ModuleInstaller;
* // use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;
*
* Developpers use declarative method to define objects, parameters, controllers... needed in this module
*/

class Stripe_official extends PaymentModule
{
    /**
    * Stripe Prestashop configuration
    * use Configuration::get(Stripe_official::CONST_NAME) to return a value
    */
    const KEY = 'STRIPE_KEY';
    const TEST_KEY = 'STRIPE_TEST_KEY';
    const PUBLISHABLE = 'STRIPE_PUBLISHABLE';
    const TEST_PUBLISHABLE = 'STRIPE_TEST_PUBLISHABLE';
    const PARTIAL_REFUND_STATE = 'STRIPE_PARTIAL_REFUND_STATE';
    const OS_SOFORT_WAITING = 'STRIPE_OS_SOFORT_WAITING';
    const MODE = 'STRIPE_MODE';
    const MINIMUM_AMOUNT_3DS = 'STRIPE_MINIMUM_AMOUNT_3DS';
    const ENABLE_IDEAL = 'STRIPE_ENABLE_IDEAL';
    const ENABLE_SOFORT = 'STRIPE_ENABLE_SOFORT';
    const ENABLE_GIROPAY = 'STRIPE_ENABLE_GIROPAY';
    const ENABLE_BANCONTACT = 'STRIPE_ENABLE_BANCONTACT';
    const ENABLE_APPLEPAY_GOOGLEPAY = 'STRIPE_ENABLE_APPLEPAY_GOOGLEPAY';
    const REFUND_ID = 'STRIPE_REFUND_ID';
    const REFUND_MODE = 'STRIPE_REFUND_MODE';
    const REFUND_AMOUNT = 'STRIPE_REFUND_AMOUNT';

    /**
     * List of objectModel used in this Module
     * @var array
     */
    public $objectModels = array(
        'StripePayment',
        'StripePaymentIntent',
    );

    /**
    * List of _202 classlib_ extentions
    * @var array
    */
    public $extensions = array(
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerExtension::class,
    );

    /**
    * To be retrocompatible with PS 1.7, admin tab (controllers) are defined in moduleAdminControllers
    */
    public $moduleAdminControllers = array(
        array(
            'name' => array(
                'en' => 'Logs',
                'fr' => 'Logs',
            ),
            'class_name' => 'AdminStripe_officialProcessLogger',
            'parent_class_name' => 'AdminAdvancedParameters',
            'visible' => false,
        ),
    );

    /**
     * List of ModuleFrontController used in this Module
     * Module::install() register it, after that you can edit it in BO (for rewrite if needed)
     * @var array
      */
    public $controllers = array(
        'orderFailure',
    );


    // public $orderStates = array(
    //     self::OS_SOFORT_WAITING => array(
    //         'name'        => array(
    //             'en' => 'Payment end of month',
    //             'fr' => 'Paiement à la fin du mois',
    //         ),
    //         'color'       => '#FFDD99',
    //         'logable'     => true,
    //         'unremovable' => true,
    //     ),
    // );

    /**
    * List of hooks needed in this module
    * _202 classlib_ extentions will plugged automatically hooks
    * @var array
    */
    public $hooks = array(
        'header',
        'orderConfirmation',
        'displayBackOfficeHeader',
        'displayAdminOrderTabOrder',
        'displayAdminOrderContentOrder',
        'displayAdminCartsView',
        'paymentOptions',
        'payment',
        'adminOrder',
    );

    // Read the Stripe guide: https://stripe.com/payments/payment-methods-guide
    public static $paymentMethods = array(
        'card' => array(
          'name' => 'Card',
          'flow' => 'none',
          'enable' => true
        ),
        'bancontact' => array(
          'name' => 'Bancontact',
          'flow' => 'redirect',
          'countries' => array('BE'),
          'currencies' => array('eur'),
          'enable' => self::ENABLE_BANCONTACT
        ),
        'giropay' => array(
          'name' => 'Giropay',
          'flow' => 'redirect',
          'countries' => array('DE'),
          'currencies' => array('eur'),
          'enable' => self::ENABLE_GIROPAY
        ),
        'ideal' => array(
          'name' => 'iDEAL',
          'flow' => 'redirect',
          'countries' => array('NL'),
          'currencies' => array('eur'),
          'enable' => self::ENABLE_IDEAL
        ),
        'sofort' => array(
          'name' => 'SOFORT',
          'flow' => 'redirect',
          'countries' => array('DE', 'AT'),
          'currencies' => array('eur'),
          'enable' => self::ENABLE_SOFORT
        ),
        /**
        * not yet implemented
        'alipay' => array(
            'name' => 'Alipay', 'flow' => 'redirect',
            'countries' => array('CN', 'HK', 'SG', 'JP'),
            'currencies' => array('aud', 'cad', 'eur', 'gbp', 'hkd', 'jpy', 'nzd', 'sgd', 'usd')
        ),
        'eps' => array(
            'name' => 'EPS', 'flow' => 'redirect',
            'countries' => array('AT'),
            'currencies' => array('eur')
        ),
        'multibanco' => array(
            'name' => 'Multibanco', 'flow' => 'receiver',
            'countries' => array('PT'),
            'currencies' => array('eur'),
        ),
        'wechat' => array(
            'name' => 'WeChat', 'flow' => 'none',
            'countries' => array('CN', 'HK', 'SG', 'JP'),
            'currencies' => array('aud', 'cad', 'eur', 'gbp', 'hkd', 'jpy', 'nzd', 'sgd', 'usd'),
        ),
        // In BETA on request only. TODO: check what to do about this one
        'sepa_debit' => array(
            'name' => 'SEPA Direct Debit', 'flow' => 'none',
            'countries' => array('FR', 'DE', 'ES', 'BE', 'NL', 'LU', 'IT', 'PT', 'AT', 'IE', 'FI'),
            'currencies' => array('eur')
        ),
        */
    );

    /* refund */
    // @todo verify if already in use
    protected $refund = 0;

    public $errors = array();

    public $warning = array();

    public $success;

    public function __construct()
    {
        $this->name = 'stripe_official';
        $this->tab = 'payments_gateways';
        $this->version = '@version@';
        $this->author = '202 ecommerce';
        $this->bootstrap = true;
        $this->display = 'view';
        $this->module_key = 'bb21cb93bbac29159ef3af00bca52354';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.9.99');
        $this->currencies = true;

        /* curl check */
        if (is_callable('curl_init') === false) {
            $this->errors[] = $this->l('To be able to use this module, please activate cURL (PHP extension).');
        }

        parent::__construct();

        $this->meta_title = $this->l('Stripe', $this->name);
        $this->displayName = $this->l('Stripe payment module', $this->name);
        $this->description = $this->l('Start accepting stripe payments today, directly from your shop!', $this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);

        /* Use a specific name to bypass an Order confirmation controller check */
        $bypassControllers = array('orderconfirmation', 'order-confirmation');
        if (in_array(Tools::getValue('controller'), $bypassControllers)) {
            $this->displayName = $this->l('Payment by Stripe', $this->name);
        }
        // Do not call Stripe Instance if API keys are not already configured
        if (self::isWellConfigured()) {
            \Stripe\Stripe::setApiKey($this->getSecretKey());
            $version = $this->version.'_'._PS_VERSION_.'_'.phpversion();
            \Stripe\Stripe::setAppInfo('StripePrestashop', $version, Configuration::get('PS_SHOP_DOMAIN_SSL'));
        }
    }

    /**
     * Check if configuration is completed. If not, disabled frontend features.
     */
    public static function isWellConfigured()
    {
        if (Configuration::get(self::MODE) && !empty(Configuration::get(self::TEST_PUBLISHABLE))) {
            return true;
        } elseif (!empty(Configuration::get(self::PUBLISHABLE))) {
            return true;
        }

        return false;
    }

    /**
     * install module depends _202 classlib_ to install hooks, objects models tables, admin controller, ...
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Stripe_officialClasslib\Install\ModuleInstaller($this);
        $installer->install();

        // preset default values
        if (!Configuration::updateValue(self::MODE, 1)
            || !Configuration::updateValue(self::REFUND_MODE, 1)
            || !Configuration::updateValue(self::MINIMUM_AMOUNT_3DS, 50)
            || !Configuration::updateValue(self::ENABLE_IDEAL, 0)
            || !Configuration::updateValue(self::ENABLE_SOFORT, 0)
            || !Configuration::updateValue(self::ENABLE_GIROPAY, 0)
            || !Configuration::updateValue(self::ENABLE_BANCONTACT, 0)) {
                 return false;
        }

        if (!$this->installOrderState()) {
            return false;
        }

        return true;
    }

    /**
     * install module depends _202 classlib_ to remove hooks, admin controller
     * please note objects models tables are NOT removed to keep payments data
     */
    public function uninstall()
    {
        $installer = new Stripe_officialClasslib\Install\ModuleInstaller($this);

        $result = parent::uninstall();
        $result &= $installer->uninstallModuleAdminControllers();
        $result &= $installer->uninstallConfiguration();

        return $result
            && Configuration::deleteByName(self::MODE)
            && Configuration::deleteByName(self::REFUND_MODE)
            && Configuration::deleteByName(self::ENABLE_IDEAL)
            && Configuration::deleteByName(self::ENABLE_SOFORT)
            && Configuration::deleteByName(self::ENABLE_GIROPAY)
            && Configuration::deleteByName(self::ENABLE_BANCONTACT);
    }

    /**
     * Create order state
     * @return boolean
     */
    public function installOrderState()
    {
        // @todo please verify condition in case of an upgrade module.
        // Perhaps we can add in upgrade 2.0.0 an initialization of OS_SOFORT_WAITING
        if (!Configuration::get(self::OS_SOFORT_WAITING)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::OS_SOFORT_WAITING)))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                // @todo find translations for all languages
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'fr':
                        $order_state->name[$language['id_lang']] = 'En attente de paiement Sofort';
                        break;
                    case 'es':
                        $order_state->name[$language['id_lang']] = 'Esperando pago Sofort';
                        break;
                    case 'de':
                        $order_state->name[$language['id_lang']] = 'Warten auf Zahlung Sofort';
                        break;
                    case 'nl':
                        $order_state->name[$language['id_lang']] = 'Wachten op betaling Sofort';
                        break;
                    case 'it':
                        $order_state->name[$language['id_lang']] = 'In attesa di pagamento Sofort';
                        break;

                    default:
                        $order_state->name[$language['id_lang']] = 'Awaiting for Sofort payment';
                        break;
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
            Configuration::updateValue(self::OS_SOFORT_WAITING, (int) $order_state->id);
        }

        /* Create Order State for Stripe */
        // @todo please verify condition in case of an upgrade module.
        // Perhaps we can add in upgrade 2.0.0 an initialization of PARTIAL_REFUND_STATE
        if (!Configuration::get(self::PARTIAL_REFUND_STATE)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::PARTIAL_REFUND_STATE)))) {
            $order_state = new OrderState();
            $langs = Language::getLanguages();
            foreach ($langs as $lang) {
                // @todo manage translations for all languages
                $order_state->name[$lang['id_lang']] = pSQL('Stripe Partial Refund');
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->logable = true;
            $order_state->color = '#FFDD99';
            $order_state->save();

            Configuration::updateValue(self::PARTIAL_REFUND_STATE, $order_state->id);
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
            $this->warning[] = $this->l('You must enable SSL on the store if you want to use this module in production.', $this->name);
        }

        /* Check if TLS is enabled and the TLS version used is 1.2 */
        if (self::isWellConfigured()) {
            try {
                \Stripe\Charge::all();
            } catch (\Stripe\Error\ApiConnection $e) {
                $this->warning[] = $this->l(
                    'TLS 1.2 is not supported. You will need to upgrade your integration. Please check the FAQ if you don\'t know how to do it.',
                    $this->name
                );
            }
        }

        /* Do Log In  */
        if (Tools::isSubmit('submit_login')) {
            if (Tools::getValue(self::MODE) == 1) {
                $secret_key = trim(Tools::getValue(self::TEST_KEY));
                $publishable_key = trim(Tools::getValue(self::TEST_PUBLISHABLE));

                if (!empty($secret_key) && !empty($publishable_key)) {
                    if (strpos($secret_key, 'test') !== false && strpos($publishable_key, 'test') !== false) {
                        if ($this->checkApiConnection($secret_key)) {
                            Configuration::updateValue(self::TEST_KEY, $secret_key);
                            Configuration::updateValue(self::TEST_PUBLISHABLE, $publishable_key);
                        }
                    } else {
                        $this->errors[] = $this->l('mode test with API key live');
                    }
                } else {
                    $this->errors[] = $this->l('Client ID and Secret Key fields are mandatory');
                }

                Configuration::updateValue(self::MODE, Tools::getValue(self::MODE));
            } else {
                $secret_key = trim(Tools::getValue(self::KEY));
                $publishable_key = trim(Tools::getValue(self::PUBLISHABLE));

                if (!empty($secret_key) && !empty($publishable_key)) {
                    if (strpos($secret_key, 'live') !== false && strpos($publishable_key, 'live') !== false) {
                        if ($this->checkApiConnection($secret_key)) {
                            Configuration::updateValue(self::KEY, $secret_key);
                            Configuration::updateValue(self::PUBLISHABLE, $publishable_key);
                        }
                    } else {
                        $this->errors['keys'] = $this->l('mode live with API key test');
                    }
                } else {
                    $this->errors[] = $this->l('Client ID and Secret Key fields are mandatory');
                }

                Configuration::updateValue(self::MODE, Tools::getValue(self::MODE));
            }

            if (!count($this->errors)) {
                $this->success = $this->l('Data succesfuly saved.');
            }

            Configuration::updateValue(self::ENABLE_IDEAL, Tools::getValue('ideal'));
            Configuration::updateValue(self::ENABLE_SOFORT, Tools::getValue('sofort'));
            Configuration::updateValue(self::ENABLE_GIROPAY, Tools::getValue('giropay'));
            Configuration::updateValue(self::ENABLE_BANCONTACT, Tools::getValue('bancontact'));
            Configuration::updateValue(self::ENABLE_APPLEPAY_GOOGLEPAY, Tools::getValue('applepay_googlepay'));
        }

        if (!Configuration::get(self::KEY) && !Configuration::get(self::PUBLISHABLE)
            && !Configuration::get(self::TEST_KEY) && !Configuration::get(self::TEST_PUBLISHABLE)) {
            $this->errors[] = $this->l('Keys are empty.');
        }

        /* Do Refund */
        if (Tools::isSubmit('submit_refund_id')) {
            $refund_id = Tools::getValue(self::REFUND_ID);
            if (!empty($refund_id)) {
                $refund = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            } else {
                $this->errors[] = $this->l('Please make sure to put a Stripe Id');
                return false;
            }

            if ($refund) {
                $this->refund = 1;
                Configuration::updateValue(self::REFUND_ID, Tools::getValue(self::REFUND_ID));
            } else {
                $this->refund = 0;
                $this->errors[] = $this->l('This Stipe ID doesn\'t exist, please check it again');
                Configuration::updateValue(self::REFUND_ID, '');
            }

            $amount = null;
            $mode = Tools::getValue(self::REFUND_MODE);
            if ($mode == 0) {
                $amount = Tools::getValue(self::REFUND_AMOUNT);
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

        if ((Configuration::get(self::TEST_KEY) != '' && Configuration::get(self::TEST_PUBLISHABLE) != '')
            || (Configuration::get(self::KEY) != '' && Configuration::get(self::PUBLISHABLE) != '')) {
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

    /**
     * Display Form
     */
    protected function assignSmartyVars()
    {

        $this->context->smarty->assign(array(
            'stripe_mode' => Configuration::get(self::MODE),
            'stripe_key' => Configuration::get(self::KEY),
            'stripe_publishable' => Configuration::get(self::PUBLISHABLE),
            'stripe_test_publishable' => Configuration::get(self::TEST_PUBLISHABLE),
            'stripe_test_key' => Configuration::get(self::TEST_KEY),
            'ideal' => Configuration::get(self::ENABLE_IDEAL),
            'sofort' => Configuration::get(self::ENABLE_SOFORT),
            'giropay' => Configuration::get(self::ENABLE_GIROPAY),
            'bancontact' => Configuration::get(self::ENABLE_BANCONTACT),
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY),
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
                    'name' => self::REFUND_ID,
                    'class' => 'fixed-width-xxl',
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'desc' => '<i>'.$this->l('We’ll submit any refund you make to your customer’s bank immediately.').'<br>'.
                        $this->l('Your customer will then receive the funds from a refund approximately 2-3 business days after the date on which the refund was initiated.').'<br>'.
                        $this->l('Refunds take 5 to 10 days to appear on your cutomer’s statement.').'</i>',
                    'name' => self::REFUND_MODE,
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
                    'name' => self::REFUND_AMOUNT,
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
            self::REFUND_ID => Configuration::get(self::REFUND_ID),
            self::REFUND_MODE => Configuration::get(self::REFUND_MODE),
            self::REFUND_AMOUNT => Configuration::get(self::REFUND_AMOUNT),
        );

        $this->context->smarty->assign(
            'refund_form',
            $this->renderGenericForm($fields_form, $fields_value, $submit_action)
        );
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
            $return_url = urlencode($domain.$_SERVER['REQUEST_URI']);
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
    public function renderGenericForm($fields_form, $fields_value = array(), $submit = false, array $tpl_vars = array())
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

        if ($submit) {
            $helper->submit_action = $submit;
        }

        $helper->tpl_vars = array_merge(array(
            'fields_value' => $fields_value,
            'id_language' => $this->context->language->id,
            'back_url' => $this->context->link->getAdminLink('AdminModules')
            .'&configure='.$this->name
            .'&tab_module='.$this->tab
            .'&module_name='.$this->name
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
        if ($this->checkApiConnection($this->getSecretKey()) && !empty($refund_id)) {
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
                $order->setCurrentState(Configuration::get(self::PARTIAL_REFUND_STATE));
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

    /**
     * get Secret Key according MODE staging or live
     *
     * @return string
     */
    public function getSecretKey()
    {
        if (Configuration::get(self::MODE)) {
            return Configuration::get(self::TEST_KEY);
        } else {
            return Configuration::get(self::KEY);
        }
    }

    /**
     * get Publishable Key according MODE staging or live
     *
     * @return string
     */
    public function getPublishableKey()
    {
        if (Configuration::get(self::MODE)) {
            return Configuration::get(self::TEST_PUBLISHABLE);
        } else {
            return Configuration::get(self::PUBLISHABLE);
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

    /**
     * @todo to move into upgrade 2.0.0
     * @todo remove upgrade 1.6.0
     * update old
     */
    public function updateConfigurationKey($oldKey, $newKey)
    {
        if (Configuration::hasKey($oldKey)) {
            $set = '';

            if ($oldKey == '_PS_STRIPE_secure' && Configuration::get($oldKey) == '0') {
                $set = ',`value` = 2';
            }

            $sql = 'UPDATE `'._DB_PREFIX_.'configuration`
                    SET `name`="'.pSQL($newKey).'"'.$set.'
                    WHERE `name`="'.pSQL($oldKey).'"';

            return Db::getInstance()->execute($sql);
        }
    }

    /**
    * Retrieve the current payment intent or create a new one
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
            // @todo change with stripe logger
            error_log($e->getMessage());
        }
    }

    /**
     * Set JS var in backoffice
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        if (Tools::getIsset('controller') &&
            Tools::getValue('controller') == 'AdminModules' &&
            Tools::getIsset('configure') &&
            Tools::getValue('configure') == $this->name) {

            Media::addJsDef(array(
                'transaction_refresh_url' => $this->context->link->getAdminLink(
                            'AdminAjaxTransaction',
                            true,
                            array(),
                            array('ajax' => 1, 'action' => 'refresh')
                  ),
            ));
        }
    }

    /**
     * Add a tab to controle intents on a cart details admin page
     */
    public function hookDisplayAdminCartsView($params)
    {
        $stripePayment = new StripePayment();
        $paymentInformations = $stripePayment->getStripePaymentByCart($params['cart']->id);

        if (empty($paymentInformations->getIdStripe())) {
            return;
        }

        $paymentInformations->state = $paymentInformations->state ? 'TEST' : 'LIVE';
        $paymentInformations->url_dashboard = $stripePayment->getDashboardUrl();

        $this->context->smarty->assign(array(
            'paymentInformations' => $paymentInformations
        ));

        return $this->display(__FILE__, 'views/templates/hook/admin_cart.tpl');
    }

    /**
     * Add a tab to controle intents on an order details admin page (tab header)
     * @return html
     */
    public function hookDisplayAdminOrderTabOrder()
    {
        return $this->display(__FILE__, 'views/templates/hook/admin_tab_order.tpl');
    }

    /**
     * Add a tab to controle intents on an order details admin page (tab content)
     * @return html
     */
    public function hookDisplayAdminOrderContentOrder($params)
    {
        $stripePayment = new StripePayment();
        $stripePayment->getStripePaymentByCart($params['order']->id_cart);
        $this->context->smarty->assign(array(
            'stripe_charge' => $stripePayment->getIdStripe(),
            'stripe_paymentIntent' => $stripePayment->getIdPaymentIntent(),
            'stripe_date' => $stripePayment->getDateAdd(),
            'stripe_dashboardUrl' => $stripePayment->getDashboardUrl(),
            'stripe_paymentType' => $stripePayment->getType()
        ));

        return $this->display(__FILE__, 'views/templates/hook/admin_content_order.tpl');
    }

    /**
     * Load JS on the front office order page
     */
    public function hookHeader()
    {
        if ($this->context->controller->php_self != 'order') {
            return;
        }

        if (!self::isWellConfigured() || !$this->active) {
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

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->context->controller->registerJavascript($this->name.'-stripe-v3', 'https://js.stripe.com/v3/', array('server'=>'remote'));
            $this->context->controller->registerJavascript($this->name.'-payments', 'modules/'.$this->name.'/views/js/payments.js');

            if (Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY)) {
                $this->context->controller->registerJavascript($this->name.'-stripepaymentrequest', 'modules/'.$this->name.'/views/js/payment_request.js');
            }

            $this->context->controller->registerStylesheet($this->name.'-checkoutcss', 'modules/'.$this->name.'/views/css/checkout.css');
            $prestashop_version = '1.7';
        } else {
            $this->context->controller->addJS('https://js.stripe.com/v3/');
            $this->context->controller->addJS($this->_path . '/views/js/payments.js');

            if (Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY)) {
                $this->context->controller->addJS($this->_path . '/views/js/payment_request.js');
            }

            $this->context->controller->addCSS($this->_path . '/views/css/checkout.css', 'all');
            $prestashop_version = '1.6';
        }

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

          'stripe_css' => '{"base": {"iconColor": "#666ee8","color": "#31325f","fontWeight": 400,"fontFamily": "-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif","fontSmoothing": "antialiased","fontSize": "15px","::placeholder": { "color": "#aab7c4" },":-webkit-autofill": { "color": "#666ee8" }}}',

          'prestashop_version' => $prestashop_version
        ));

    }

    /**
     * Hook Stripe Payment for PS 1.6
     */
    public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkApiConnection()) {
            $this->context->smarty->assign(array(
                'stripeError' => $this->l('No API keys have been provided. Please contact the owner of the website.', $this->name)
            ));
        }

        $this->context->smarty->assign(array(
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY),
            'prestashop_version' => '1.6',
        ));

        // Fetch country based on invoice address and currency
        $address = new Address($params['cart']->id_address_invoice);
        $country = Country::getIsoById($address->id_country);
        $currency = strtolower($this->context->currency->iso_code);

        // Show only the payment methods that are relevant to the selected country and currency
        $display = '';
        foreach (self::$paymentMethods as $name => $paymentMethod) {
            // Check if the payment method is enabled
            if ($paymentMethod['enable'] !== true && Configuration::get($paymentMethod['enable']) != 'on') {
                continue;
            }

            // Check for country support
            if (isset($paymentMethod['countries']) && !in_array($country, $paymentMethod['countries'])) {
                continue;
            }

            // Check for currency support
            if (isset($paymentMethod['currencies']) && !in_array($currency, $paymentMethod['currencies'])) {
                continue;
            }

            $display .= $this->display(__FILE__, 'views/templates/front/payment_form_' . basename($name) . '.tpl');
        }

        return $display;
    }

    /**
     * Hook Stripe Payment for PS 1.7
    */
    public function hookPaymentOptions($params)
    {
        if (!self::isWellConfigured() || !$this->active) {
            return;
        }

        if (!$this->checkApiConnection()) {
            $this->context->smarty->assign(array(
                'stripeError' => $this->l('No API keys have been provided. Please contact the owner of the website.', $this->name)
            ));
        }

        $this->context->smarty->assign(array(
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY),
            'prestashop_version' => '1.7',
            'publishableKey' => $this->getPublishableKey()
        ));

        // Fetch country based on invoice address and currency
        $address = new Address($params['cart']->id_address_invoice);
        $country = Country::getIsoById($address->id_country);
        $currency = strtolower($this->context->currency->iso_code);

        // Show only the payment methods that are relevant to the selected country and currency
        $options = array();
        foreach (self::$paymentMethods as $name => $paymentMethod) {
            // Check if the payment method is enabled
            if ($paymentMethod['enable'] !== true && Configuration::get($paymentMethod['enable']) != 'on') {
                continue;
            }

            // Check for country support
            if (isset($paymentMethod['countries']) && !in_array($country, $paymentMethod['countries'])) {
                continue;
            }

            // Check for currency support
            if (isset($paymentMethod['currencies']) && !in_array($currency, $paymentMethod['currencies'])) {
                continue;
            }

            // The customer can potientially use this payment method
            $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $option
            ->setModuleName($this->name)
            //->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/'.$cc_img))
            ->setCallToActionText($this->l('Pay by ' . $paymentMethod['name']));

            // Display additional information for redirect and receiver based payment methods
            if (in_array($paymentMethod['flow'], array('redirect', 'receiver'))) {
                $option->setAdditionalInformation(
                    $this->context->smarty->fetch(
                        'module:' . $this->name . '/views/templates/front/payment_info_' . basename($paymentMethod['flow']) . '.tpl'
                    )
                );
            }

            // Payment methods with embedded form fields
            $option->setForm($this->context->smarty->fetch(
                'module:' . $this->name . '/views/templates/front/payment_form_' .  basename($name) . '.tpl')
            );

            $options[] = $option;
        }

        return $options;
    }

    /**
     * Hook Order Confirmation
     */
    public function hookOrderConfirmation($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $order = $params['order'];
        } else {
            $order = $params['objOrder'];
        }

        if (!self::isWellConfigured() || !$this->active || $order->module != $this->name) {
            return;
        }

        $this->context->smarty->assign('stripe_order_reference', pSQL($order->reference));

        return $this->display(__FILE__, 'views/templates/front/order-confirmation.tpl');
    }
}
