<?php
/**
 * 2007-2022 Stripe
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
 * @license   Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

/**
 * Stripe object model
 */

// use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

/**
 * Stripe official PrestaShop module main class extends payment class
 * Please note this module use _202 PrestaShop Classlib Project_ (202 classlib) a library developed by "202 ecommerce"
 * This library provide utils common features as DB installer, internal logger, chain of responsibility design pattern
 *
 * To let module compatible with Prestashop 1.6 please keep this following line commented in PrestaShop 1.6:
 * // use Stripe_officialClasslib\Install\ModuleInstaller;
 * // use Stripe_officialClasslib\Actions\ActionsHandler;
 * // use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;
 *
 * Developers use declarative method to define objects, parameters, controllers... needed in this module
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
    const OS_SOFORT_WAITING = 'STRIPE_OS_SOFORT_WAITING';
    const CAPTURE_WAITING = 'STRIPE_CAPTURE_WAITING';
    const SEPA_WAITING = 'STRIPE_SEPA_WAITING';
    const SEPA_DISPUTE = 'STRIPE_SEPA_DISPUTE';
    const OXXO_WAITING = 'STRIPE_OXXO_WAITING';
    const MODE = 'STRIPE_MODE';
    const MINIMUM_AMOUNT_3DS = 'STRIPE_MINIMUM_AMOUNT_3DS';
    const POSTCODE = 'STRIPE_POSTCODE';
    const CARDHOLDERNAME = 'STRIPE_CARDHOLDERNAME';
    const REINSURANCE = 'STRIPE_REINSURANCE';
    const VISA = 'STRIPE_PAYMENT_VISA';
    const MASTERCARD = 'STRIPE_PAYMENT_MASTERCARD';
    const AMERICAN_EXPRESS = 'STRIPE_PAYMENT_AMERICAN_EXPRESS';
    const CB = 'STRIPE_PAYMENT_CB';
    const DINERS_CLUB = 'STRIPE_PAYMENT_DINERS_CLUB';
    const UNION_PAY = 'STRIPE_PAYMENT_UNION_PAY';
    const JCB = 'STRIPE_PAYMENT_JCB';
    const DISCOVERS = 'STRIPE_PAYMENT_DISCOVERS';
    const ENABLE_IDEAL = 'STRIPE_ENABLE_IDEAL';
    const ENABLE_SOFORT = 'STRIPE_ENABLE_SOFORT';
    const ENABLE_GIROPAY = 'STRIPE_ENABLE_GIROPAY';
    const ENABLE_BANCONTACT = 'STRIPE_ENABLE_BANCONTACT';
    const ENABLE_FPX = 'STRIPE_ENABLE_FPX';
    const ENABLE_EPS = 'STRIPE_ENABLE_EPS';
    const ENABLE_P24 = 'STRIPE_ENABLE_P24';
    const ENABLE_SEPA = 'STRIPE_ENABLE_SEPA';
    const ENABLE_ALIPAY = 'STRIPE_ENABLE_ALIPAY';
    const ENABLE_OXXO = 'STRIPE_ENABLE_OXXO';
    const ENABLE_APPLEPAY_GOOGLEPAY = 'STRIPE_ENABLE_APPLEPAY_GOOGLEPAY';
    const REFUND_ID = 'STRIPE_REFUND_ID';
    const REFUND_MODE = 'STRIPE_REFUND_MODE';
    const REFUND_AMOUNT = 'STRIPE_REFUND_AMOUNT';
    const CATCHANDAUTHORIZE = 'STRIPE_CATCHANDAUTHORIZE';
    const CAPTURE_STATUS = 'STRIPE_CAPTURE_STATUS';
    const CAPTURE_EXPIRE = 'STRIPE_CAPTURE_EXPIRE';
    const SAVE_CARD = 'STRIPE_SAVE_CARD';
    const ASK_CUSTOMER = 'STRIPE_ASK_CUSTOMER';
    const WEBHOOK_SIGNATURE = 'STRIPE_WEBHOOK_SIGNATURE';
    const WEBHOOK_ID = 'STRIPE_WEBHOOK_ID';
    const ACCOUNT_ID = 'STRIPE_ACCOUNT_ID';

    /**
     * List of objectModel used in this Module
     *
     * @var array
     */
    public $objectModels = [
        'StripePayment',
        'StripePaymentIntent',
        'StripeCapture',
        'StripeCustomer',
        'StripeIdempotencyKey',
        'StripeEvent',
    ];

    /**
     * List of _202 classlib_ extentions
     *
     * @var array
     */
    public $extensions = [
        Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerExtension::class,
    ];

    /**
     * To be retrocompatible with PS 1.7, admin tab (controllers) are defined in moduleAdminControllers
     */
    public $moduleAdminControllers = [
        [
            'name' => [
                'en' => 'Logs',
                'fr' => 'Logs',
            ],
            'class_name' => 'AdminStripe_officialProcessLogger',
            'parent_class_name' => 'stripe_official',
            'visible' => false,
        ],
    ];

    /**
     * List of ModuleFrontController used in this Module
     * Module::install() register it, after that you can edit it in BO (for rewrite if needed)
     *
     * @var array
     */
    public $controllers = [
        'orderFailure',
        'stripeCards',
    ];

    /**
     * List of hooks needed in this module
     * _202 classlib_ extentions will plugged automatically hooks
     *
     * @var array
     */
    public $hooks = [
        'header',
        'orderConfirmation',
        'displayBackOfficeHeader',
        'displayAdminOrderTabOrder',
        'displayAdminOrderContentOrder',
        'displayAdminOrderTabLink',
        'displayAdminOrderTabContent',
        'displayAdminCartsView',
        'paymentOptions',
        'payment',
        'displayPaymentEU',
        'adminOrder',
        'actionOrderStatusUpdate',
        'displayMyAccountBlock',
        'displayCustomerAccount',
    ];

    // Read the Stripe guide: https://stripe.com/payments/payment-methods-guide
    public static $paymentMethods = [
        'card' => [
            'name' => 'Card',
            'flow' => 'none',
            'enable' => true,
            'catch_enable' => true,
            'display_in_back_office' => false,
        ],
        'alipay' => [
            'name' => 'Alipay',
            'flow' => 'redirect',
            'countries' => ['CN'],
            'countries_names' => [
                'en' => 'China',
                'fr' => 'Chine',
                'de' => 'China',
                'es' => 'China',
                'it' => 'Cina',
                'nl' => 'China',
            ],
            'currencies' => ['cny', 'aud', 'cad', 'eur', 'gbp', 'hkd', 'jpy', 'sgd', 'myr', 'nzd', 'usd'],
            'enable' => self::ENABLE_ALIPAY,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'Yes',
        ],
        'bancontact' => [
            'name' => 'Bancontact',
            'flow' => 'redirect',
            'countries' => ['BE'],
            'countries_names' => [
                'en' => 'Belgium',
                'fr' => 'Belgique',
                'de' => 'Belgien',
                'es' => 'Bélgica',
                'it' => 'Belgio',
                'nl' => 'België',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_BANCONTACT,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'eps' => [
            'name' => 'EPS',
            'flow' => 'redirect',
            'countries' => ['AT'],
            'countries_names' => [
                'en' => 'Austria',
                'fr' => 'Autriche',
                'de' => 'Österreich',
                'es' => 'Austria',
                'it' => 'Austria',
                'nl' => 'Oostenrijk',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_EPS,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'fpx' => [
            'name' => 'FPX',
            'flow' => 'redirect',
            'countries' => ['MY'],
            'countries_names' => [
                'en' => 'Malaysia',
                'fr' => 'Malaisie',
                'de' => 'Malaysia',
                'es' => 'Malasia',
                'it' => 'Malesia',
                'nl' => 'Malaysia',
            ],
            'currencies' => ['myr'],
            'enable' => self::ENABLE_FPX,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'Yes',
            'new_payment' => 'No',
        ],
        'giropay' => [
            'name' => 'Giropay',
            'flow' => 'redirect',
            'countries' => ['DE'],
            'countries_names' => [
                'en' => 'Germany',
                'fr' => 'Allemagne',
                'de' => 'Deutschland',
                'es' => 'Alemania',
                'it' => 'Germania',
                'nl' => 'Duitsland',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_GIROPAY,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'ideal' => [
            'name' => 'iDEAL',
            'flow' => 'redirect',
            'countries' => ['NL'],
            'countries_names' => [
                'en' => 'Netherlands',
                'fr' => 'Pays-Bas',
                'de' => 'Niederlande',
                'es' => 'Países Bajos',
                'it' => 'Paesi Bassi',
                'nl' => 'Nederlande',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_IDEAL,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'oxxo' => [
            'name' => 'OXXO',
            'flow' => 'voucher',
            'countries' => ['MX'],
            'countries_names' => [
                'en' => 'Mexico',
                'fr' => 'Mexique',
                'de' => 'Mexico',
                'es' => 'Mexico',
                'it' => 'Mexico',
                'nl' => 'Mexico',
            ],
            'currencies' => ['mxn'],
            'enable' => self::ENABLE_OXXO,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'Yes',
            'new_payment' => 'Yes',
        ],
        'p24' => [
            'name' => 'P24',
            'flow' => 'redirect',
            'countries' => ['PL'],
            'countries_names' => [
                'en' => 'Poland',
                'fr' => 'Pologne',
                'de' => 'Polen',
                'es' => 'Polonia',
                'it' => 'Polonia',
                'nl' => 'Polen',
            ],
            'currencies' => ['pln'],
            'enable' => self::ENABLE_P24,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'sepa_debit' => [
            'name' => 'SEPA Direct Debit',
            'flow' => 'none',
            'countries' => ['FR', 'DE', 'ES', 'BE', 'NL', 'LU', 'IT', 'PT', 'AT', 'IE'],
            'countries_names' => [
                'en' => 'France, Germany, Spain, Belgium, Netherlands, Luxembourg, Italy, Portugal, Austria, Ireland',
                'fr' => 'France, Allemagne, Espagne, Belgique, Pays-Bas, Luxembourg, Italie, Portugal, Autriche, Irlande',
                'de' => 'Frankreich, Deutschland, Spanien, Belgien, Niederlande, Luxemburg, Italien, Portugal, Österreich, Irland',
                'es' => 'Francia, Alemania, España, Bélgica, Países Bajos, Luxemburgo, Italia, Portugal, Austria, Irlanda',
                'it' => 'Francia, Germania, Spagna, Belgio, Paesi Bassi, Lussemburgo, Italia, Portogallo, Austria, Irlanda',
                'nl' => 'Frankrijk, Duitsland, Spanje, België, Nederland, Luxemburg, Italië, Portugal, Oostenrijk, Ierland',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_SEPA,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
        'sofort' => [
            'name' => 'SOFORT',
            'flow' => 'redirect',
            'countries' => ['AT', 'BE', 'DE', 'IT', 'NL', 'ES'],
            'countries_names' => [
                'en' => 'Austria, Belgium, Germany, Italy, Netherlands, Spain',
                'fr' => 'Autriche, Belgique, Allemagne, Italie, Pays-Bas, Espagne',
                'de' => 'Österreich, Belgien, Deutschland, Italien, Niederlande, Spanien',
                'es' => 'Austria, Bélgica, Alemania, Italia, Países Bajos, España',
                'it' => 'Austria, Belgio, Germania, Italia, Paesi Bassi, Spagna',
                'nl' => 'Österreich, België, Deutschland, Italië, Nederland, Spanje',
            ],
            'currencies' => ['eur'],
            'enable' => self::ENABLE_SOFORT,
            'catch_enable' => false,
            'display_in_back_office' => true,
            'require_activation' => 'No',
            'new_payment' => 'No',
        ],
    ];

    public static $webhook_events = [
        \Stripe\Event::CHARGE_EXPIRED,
        \Stripe\Event::CHARGE_FAILED,
        \Stripe\Event::CHARGE_SUCCEEDED,
        \Stripe\Event::CHARGE_PENDING,
        \Stripe\Event::CHARGE_CAPTURED,
        \Stripe\Event::CHARGE_REFUNDED,
        \Stripe\Event::CHARGE_DISPUTE_CREATED,
        \Stripe\Event::PAYMENT_INTENT_REQUIRES_ACTION,
    ];

    /* refund */
    protected $refund = 0;

    public $errors = [];

    public $warning = [];

    public $success;

    public $display;

    public $meta_title;

    public $button_label = [];

    public function __construct()
    {
        $this->name = 'stripe_official';
        $this->tab = 'payments_gateways';
        $this->version = '@version@';
        $this->author = '202 ecommerce';
        $this->bootstrap = true;
        $this->display = 'view';
        $this->module_key = 'bb21cb93bbac29159ef3af00bca52354';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => '1.7.9.99'];
        $this->currencies = true;

        /* curl check */
        if (is_callable('curl_init') === false) {
            $this->errors[] = $this->l('To be able to use this module, please activate cURL (PHP extension).');
        }

        parent::__construct();

        $this->button_label['card'] = $this->l('Pay by card');
        $this->button_label['bancontact'] = $this->l('Pay by Bancontact');
        $this->button_label['giropay'] = $this->l('Pay by Giropay');
        $this->button_label['ideal'] = $this->l('Pay by iDEAL');
        $this->button_label['sofort'] = $this->l('Pay by SOFORT');
        $this->button_label['fpx'] = $this->l('Pay by FPX');
        $this->button_label['eps'] = $this->l('Pay by EPS');
        $this->button_label['p24'] = $this->l('Pay by P24');
        $this->button_label['sepa_debit'] = $this->l('Pay by SEPA Direct Debit');
        $this->button_label['alipay'] = $this->l('Pay by Alipay');
        $this->button_label['oxxo'] = $this->l('Pay by OXXO');
        $this->button_label['save_card'] = $this->l('Pay with card');

        $this->meta_title = $this->l('Stripe', $this->name);
        $this->displayName = $this->l('Stripe payment module', $this->name);
        $this->description = $this->l('Start accepting stripe payments today, directly from your shop!', $this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);

        require_once realpath(dirname(__FILE__) . '/smarty/plugins') . '/modifier.stripelreplace.php';

        /* Use a specific name to bypass an Order confirmation controller check */
        $bypassControllers = ['orderconfirmation', 'order-confirmation'];
        if (in_array(Tools::getValue('controller'), $bypassControllers)) {
            $this->displayName = $this->l('Payment by Stripe', $this->name);
        }
        // Do not call Stripe Instance if API keys are not already configured
        if (self::isWellConfigured()) {
            try {
                \Stripe\Stripe::setApiKey($this->getSecretKey());
                $version = $this->version . '_' . _PS_VERSION_ . '_' . phpversion();
                \Stripe\Stripe::setAppInfo(
                    'StripePrestashop',
                    $version,
                    'https://addons.prestashop.com/en/payment-card-wallet/24922-stripe-official.html',
                    'pp_partner_EX2Z2idAZw7OWr'
                );
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                    'Fail to set API Key. Stripe SDK return error: ' . $e
                );
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();
            }
        }
    }

    /**
     * Check if configuration is completed. If not, disabled frontend features.
     */
    public static function isWellConfigured()
    {
        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();
        $mode = Configuration::get(self::MODE, null, $shopGroupId, $shopId);
        if ($mode == '1' && !empty(Configuration::get(self::TEST_PUBLISHABLE, null, $shopGroupId, $shopId))) {
            return true;
        } elseif ($mode == '0' && !empty(Configuration::get(self::PUBLISHABLE, null, $shopGroupId, $shopId))) {
            return true;
        }

        return false;
    }

    /**
     * install module depends _202 classlib_ to install hooks, objects models tables, admin controller, ...
     */
    public function install()
    {
        try {
            if (!parent::install()) {
                return false;
            }
        } catch (PrestaShopDatabaseException $e) {
            PrestaShopLogger::addLog($e->getMessage() . $e->getTraceAsString(), 1);
        } catch (PrestaShopException $e) {
            PrestaShopLogger::addLog($e->getMessage() . $e->getTraceAsString(), 1);
        }

        try {
            $installer = new Stripe_officialClasslib\Install\ModuleInstaller($this);

            if (!$installer->install()) {
                return false;
            }

            $sql = 'SHOW KEYS FROM `' . _DB_PREFIX_ . "stripe_event` WHERE Key_name = 'ix_id_payment_intentstatus'";

            if (!Db::getInstance()->executeS($sql)) {
                $sql = 'SELECT MAX(id_stripe_event) AS id_stripe_event FROM `' . _DB_PREFIX_ . 'stripe_event` GROUP BY `id_payment_intent`, `status`';
                $duplicateRows = Db::getInstance()->executeS($sql);

                $idList = array_column($duplicateRows, 'id_stripe_event');

                if (!empty($idList)) {
                    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'stripe_event` WHERE id_stripe_event NOT IN (' . implode(',', $idList) . ');';
                    Db::getInstance()->execute($sql);
                }

                $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'stripe_event` ADD UNIQUE `ix_id_payment_intentstatus` (`id_payment_intent`, `status`);';
                Db::getInstance()->execute($sql);
            }

            if (Hook::getHookStatusByName('actionStripeOfficialMetadataDefinition') === false) {
                $name = 'actionStripeOfficialAddPaymentIntent';
                $title = 'Define metadata of Stripe payment intent';
                $description = 'Metadata is passing during creation and update of Stripe payment intent';
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'hook` (`name`, `title`, `description`) VALUES ("' . $name . '", "' . $title . '", "' . $description . '");';
                Db::getInstance()->execute($sql);
            }

            $shopGroupId = Stripe_official::getShopGroupIdContext();
            $shopId = Stripe_official::getShopIdContext();

            // preset default values
            if (!Configuration::updateValue(self::MODE, 1, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::REFUND_MODE, 1, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::MINIMUM_AMOUNT_3DS, 50, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_IDEAL, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_SOFORT, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_GIROPAY, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_BANCONTACT, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_FPX, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_EPS, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_P24, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_SEPA, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_ALIPAY, 0, false, $shopGroupId, $shopId)
                || !Configuration::updateValue(self::ENABLE_OXXO, 0, false, $shopGroupId, $shopId)) {
                return false;
            }

            if (!$this->installOrderState()) {
                return false;
            }

            return true;
        } catch (PrestaShopDatabaseException $e) {
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                $e->getMessage(),
                null,
                null,
                'Stripe_official - install'
            );
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

            return false;
        } catch (PrestaShopException $e) {
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                $e->getMessage(),
                null,
                null,
                'Stripe_official - install'
            );
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

            return false;
        }
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
            && Configuration::deleteByName(self::ENABLE_BANCONTACT)
            && Configuration::deleteByName(self::ENABLE_FPX)
            && Configuration::deleteByName(self::ENABLE_EPS)
            && Configuration::deleteByName(self::ENABLE_P24)
            && Configuration::deleteByName(self::ENABLE_SEPA)
            && Configuration::deleteByName(self::ENABLE_ALIPAY)
            && Configuration::deleteByName(self::ENABLE_OXXO);
    }

    /**
     * Create order state
     *
     * @return bool
     */
    public function installOrderState()
    {
        if (!Configuration::get(self::OS_SOFORT_WAITING)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::OS_SOFORT_WAITING)))) {
            $order_state = new OrderState();
            $order_state->name = [];
            foreach (Language::getLanguages() as $language) {
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
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'stripe_official/views/img/cc-sofort.png';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }
            Configuration::updateValue(self::OS_SOFORT_WAITING, (int) $order_state->id);
        }

        /* Create Order State for Stripe */
        if (!Configuration::get(self::CAPTURE_WAITING)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::CAPTURE_WAITING)))) {
            $order_state = new OrderState();
            $order_state->name = [];
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'fr':
                        $order_state->name[$language['id_lang']] = pSQL('En attente de capture Stripe');
                        break;
                    case 'es':
                        $order_state->name[$language['id_lang']] = pSQL('A la espera de captura Stripe');
                        break;
                    case 'de':
                        $order_state->name[$language['id_lang']] = pSQL('Auf Festnahme Stripe warten');
                        break;
                    case 'nl':
                        $order_state->name[$language['id_lang']] = pSQL('Wachten op opname van Stripe');
                        break;
                    case 'it':
                        $order_state->name[$language['id_lang']] = pSQL('In attesa di cattura Stripe');
                        break;

                    default:
                        $order_state->name[$language['id_lang']] = pSQL('Waiting for Stripe capture');
                        break;
                }
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->logable = true;
            $order_state->color = '#03befc';
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'stripe_official/views/img/ca_icon.gif';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }

            Configuration::updateValue(self::CAPTURE_WAITING, $order_state->id);
        }

        /* Create Order State for Stripe */
        if (!Configuration::get(self::SEPA_WAITING)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::SEPA_WAITING)))) {
            $order_state = new OrderState();
            $order_state->name = [];
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'fr':
                        $order_state->name[$language['id_lang']] = pSQL('En attente de paiement SEPA');
                        break;
                    case 'es':
                        $order_state->name[$language['id_lang']] = pSQL('Esperando pago SEPA');
                        break;
                    case 'de':
                        $order_state->name[$language['id_lang']] = pSQL('Warten auf SEPA-Zahlung');
                        break;
                    case 'nl':
                        $order_state->name[$language['id_lang']] = pSQL('Wachten op SEPA-betaling');
                        break;
                    case 'it':
                        $order_state->name[$language['id_lang']] = pSQL('In attesa del pagamento SEPA');
                        break;

                    default:
                        $order_state->name[$language['id_lang']] = pSQL('Waiting for SEPA payment');
                        break;
                }
            }
            $order_state->send_email = false;
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->color = '#fcba03';
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'stripe_official/views/img/ca_icon.gif';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }

            Configuration::updateValue(self::SEPA_WAITING, $order_state->id);
        }

        /* Create Order State for Stripe */
        if (!Configuration::get(self::SEPA_DISPUTE)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::SEPA_DISPUTE)))) {
            $order_state = new OrderState();
            $order_state->name = [];
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'fr':
                        $order_state->name[$language['id_lang']] = pSQL('Litige SEPA');
                        break;
                    case 'es':
                        $order_state->name[$language['id_lang']] = pSQL('Disputa SEPA');
                        break;
                    case 'de':
                        $order_state->name[$language['id_lang']] = pSQL('SEPA-Streit');
                        break;
                    case 'nl':
                        $order_state->name[$language['id_lang']] = pSQL('SEPA-geschil');
                        break;
                    case 'it':
                        $order_state->name[$language['id_lang']] = pSQL('Controversia SEPA');
                        break;

                    default:
                        $order_state->name[$language['id_lang']] = pSQL('SEPA dispute');
                        break;
                }
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->logable = true;
            $order_state->color = '#e3e1dc';
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'stripe_official/views/img/ca_icon.gif';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }

            Configuration::updateValue(self::SEPA_DISPUTE, $order_state->id);
        }

        /* Create Order State for Stripe */
        if (!Configuration::get(self::OXXO_WAITING)
            || !Validate::isLoadedObject(new OrderState(Configuration::get(self::OXXO_WAITING)))) {
            $order_state = new OrderState();
            $order_state->name = [];
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'fr':
                        $order_state->name[$language['id_lang']] = pSQL('En attente de la confirmation de paiement OXXO');
                        break;
                    case 'es':
                        $order_state->name[$language['id_lang']] = pSQL('Esperando la confirmación del pago de OXXO');
                        break;
                    case 'de':
                        $order_state->name[$language['id_lang']] = pSQL('Warten auf OXXO-Zahlungsbestätigung');
                        break;
                    case 'nl':
                        $order_state->name[$language['id_lang']] = pSQL('Wachten op OXXO betalingsbevestiging');
                        break;
                    case 'it':
                        $order_state->name[$language['id_lang']] = pSQL('In attesa della conferma del pagamento OXXO');
                        break;

                    default:
                        $order_state->name[$language['id_lang']] = pSQL('Waiting for OXXO payment confirmation');
                        break;
                }
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->color = '#C23416';
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'stripe_official/views/img/ca_icon.gif';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }

            Configuration::updateValue(self::OXXO_WAITING, $order_state->id);
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
            $this->warning[] = $this->l(
                'You must enable SSL on the store if you want to use this module in production.',
                $this->name
            );
        }

        /* Check if webhook limit has been reached */
        if (StripeWebhook::webhookCanBeRegistered() === false && self::isWellConfigured() === true) {
            $this->warning[] = $this->l(
                'You reached the limit of 16 webhook endpoints registered in your Dashboard Stripe for this account. Please remove one of them if you want to register this domain.',
                $this->name
            );
        }

        /* Check if TLS is enabled and the TLS version used is 1.2 */
        if (self::isWellConfigured()) {
            $secret_key = trim(Tools::getValue(self::TEST_KEY));
            if ($this->checkApiConnection($secret_key) !== false) {
                try {
                    \Stripe\Charge::all();
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logInfo($e);
                    Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

                    $this->warning[] = $this->l(
                        'Your TLS version is not supported. You will need to upgrade your integration. Please check the FAQ if you don\'t know how to do it.',
                        $this->name
                    );
                }
            }
        }

        /* Do Log In  */
        if (Tools::isSubmit('submit_login')) {
            $handler = new Stripe_officialClasslib\Actions\ActionsHandler();
            $handler->setConveyor([
                'context' => $this->context,
                'module' => $this,
            ]);

            $handler->addActions(
                'registerKeys',
                'registerCatchAndAuthorize',
                'registerSaveCard',
                'registerOtherConfigurations',
                'registerApplePayDomain',
                'registerWebhookSignature'
            );

            $handler->process('ConfigurationActions');
        }

        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();

        /* Check if webhook_id has been defined */
        $webhookId = Configuration::get(self::WEBHOOK_ID, null, $shopGroupId, $shopId);
        if (!$webhookId) {
            $this->errors[] = $this->l(
                'Webhook configuration cannot be found in PrestaShop, click on save button to fix issue. A new webhook will be created on Stripe, then saved in PrestaShop.',
                $this->name
            );
        } else {
            /* Check if webhook access is write */
            try {
                $webhookEndpoint = \Stripe\WebhookEndpoint::retrieve($webhookId);

                /* Check if webhook url is wrong */
                $expectedWebhookUrl = self::getWebhookUrl();
                if ($webhookEndpoint->url != $expectedWebhookUrl) {
                    $this->errors[] =
                        $this->l('Webhook URL configuration is wrong, click on save button to fix issue. Webhook configuration will be corrected.', $this->name) . ' | ' .
                        $this->l('Current webhook URL : ', $this->name) . $webhookEndpoint->url . ' | ' .
                        $this->l('Expected webhook URL : ', $this->name) . $expectedWebhookUrl;
                } else {
                    /* Check if webhook events are wrong */
                    $eventError = false;
                    if (count($webhookEndpoint->enabled_events) == count(Stripe_official::$webhook_events)) {
                        foreach ($webhookEndpoint->enabled_events as $webhookEvent) {
                            if (!in_array($webhookEvent, Stripe_official::$webhook_events)) {
                                $eventError = true;
                            }
                        }
                    } else {
                        $eventError = true;
                    }
                    if ($eventError) {
                        $this->errors[] =
                            $this->l('Webhook events configuration are wrong, click on save button to fix isssue. Webhook configuration will be corrected.', $this->name) . ' | ' .
                            $this->l('Current webhook events : ', $this->name) . implode(' / ', $webhookEndpoint->enabled_events) . ' | ' .
                            $this->l('Expected webhook events : ', $this->name) . implode(' / ', Stripe_official::$webhook_events);
                    }
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $this->errors[] = $this->l(
                    'Webhook configuration cannot be accessed, click on save button to fix issue. A new webhook will be created on Stripe.',
                    $this->name
                );
            }
        }

        /* Check if public and secret key have been defined */
        if (!Configuration::get(self::KEY, null, $shopGroupId, $shopId) && !Configuration::get(self::PUBLISHABLE, null, $shopGroupId, $shopId)
            && !Configuration::get(self::TEST_KEY, null, $shopGroupId, $shopId) && !Configuration::get(self::TEST_PUBLISHABLE, null, $shopGroupId, $shopId)) {
            $this->errors[] = $this->l('Keys are empty.');
        }

        /* Do Refund */
        if (Tools::isSubmit('submit_refund_id')) {
            $refund_id = Tools::getValue(self::REFUND_ID);
            if (!empty($refund_id)) {
                $query = new DbQuery();
                $query->select('*');
                $query->from('stripe_payment');
                $query->where('id_stripe = "' . pSQL($refund_id) . '"');
                $refund = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query->build());
            } else {
                $this->errors[] = $this->l('The Stripe Payment ID can\'t be empty.');

                return false;
            }

            if ($refund) {
                $this->refund = 1;
                Configuration::updateValue(self::REFUND_ID, Tools::getValue(self::REFUND_ID), false, $shopGroupId, $shopId);
            } else {
                $this->refund = 0;
                $this->errors[] = $this->l('Unknown Stripe Payment ID.');
                Configuration::updateValue(self::REFUND_ID, '', false, $shopGroupId, $shopId);
            }

            $amount = null;
            $mode = Tools::getValue(self::REFUND_MODE);
            if ($mode == 0) {
                $amount = Tools::getValue(self::REFUND_AMOUNT);
                $amount = str_replace(',', '.', $amount);
            }

            $this->apiRefund($refund[0]['id_stripe'], $refund[0]['currency'], $mode, $refund[0]['id_cart'], $amount);

            if (!count($this->errors)) {
                $this->success = $this->l('Refunds processed successfully');
            }
        }

        if (Tools::usingSecureMode()) {
            $domain = Tools::getShopDomainSsl(true, true);
        } else {
            $domain = Tools::getShopDomain(true, true);
        }

        $this->context->controller->addJS($this->_path . '/views/js/faq.js');
        $this->context->controller->addJS($this->_path . '/views/js/back.js');
        $this->context->controller->addJS($this->_path . '/views/js/PSTabs.js');

        $this->context->controller->addCSS($this->_path . '/views/css/admin.css');

        if ((Configuration::get(self::TEST_KEY, null, $shopGroupId, $shopId) != '' && Configuration::get(self::TEST_PUBLISHABLE, null, $shopGroupId, $shopId) != '')
            || (Configuration::get(self::KEY, null, $shopGroupId, $shopId) != '' && Configuration::get(self::PUBLISHABLE, null, $shopGroupId, $shopId) != '')) {
            $keys_configured = true;
        } else {
            $keys_configured = false;
        }

        $allOrderStatus = OrderState::getOrderStates($this->context->language->id);
        $statusSelected = [];
        $statusUnselected = [];

        if (Configuration::get(self::CAPTURE_STATUS, null, $shopGroupId, $shopId) && Configuration::get(self::CAPTURE_STATUS, null, $shopGroupId, $shopId) != '') {
            $capture_status = explode(',', Configuration::get(self::CAPTURE_STATUS, null, $shopGroupId, $shopId));
            foreach ($allOrderStatus as $status) {
                if (in_array($status['id_order_state'], $capture_status)) {
                    $statusSelected[] = $status;
                } else {
                    $statusUnselected[] = $status;
                }
            }
        } else {
            $statusUnselected = $allOrderStatus;
        }

        $orderStatus = [];
        $orderStatus['selected'] = $statusSelected;
        $orderStatus['unselected'] = $statusUnselected;

        $this->context->smarty->assign([
            'logo' => $domain . __PS_BASE_URI__ . basename(_PS_MODULE_DIR_) . '/' . $this->name . '/views/img/Stripe_logo.png',
            'new_base_dir', $this->_path,
            'keys_configured' => $keys_configured,
            'link' => new Link(),
            'catchandauthorize' => Configuration::get(self::CATCHANDAUTHORIZE, null, $shopGroupId, $shopId),
            'orderStatus' => $orderStatus,
            'orderStatusSelected' => Configuration::get(self::CAPTURE_STATUS, null, $shopGroupId, $shopId),
            'allOrderStatus' => $allOrderStatus,
            'captureExpire' => Configuration::get(self::CAPTURE_EXPIRE, null, $shopGroupId, $shopId),
            'save_card' => Configuration::get(self::SAVE_CARD, null, $shopGroupId, $shopId),
            'ask_customer' => Configuration::get(self::ASK_CUSTOMER, null, $shopGroupId, $shopId),
            'payment_methods' => Stripe_official::$paymentMethods,
            'language_iso_code' => $this->context->language->iso_code,
            'stripe_payments_url' => 'https://dashboard.stripe.com/settings/payments',
        ]);

        $this->displaySomething();
        $this->assignSmartyVars();

        if (count($this->warning)) {
            $this->context->smarty->assign('warnings', $this->warning);
        }

        if (!empty($this->success) && !count($this->errors)) {
            $this->context->smarty->assign('success', $this->success);
        }

        if (count($this->errors)) {
            $this->context->smarty->assign('errors', $this->errors);
        }

        return $this->display($this->_path, 'views/templates/admin/main.tpl');
    }

    /**
     * Display Form
     */
    protected function assignSmartyVars()
    {
        $shopGroupId = self::getShopGroupIdContext();
        $shopId = self::getShopIdContext();
        $this->context->smarty->assign([
            'stripe_mode' => Configuration::get(self::MODE, null, $shopGroupId, $shopId),
            'stripe_key' => Configuration::get(self::KEY, null, $shopGroupId, $shopId),
            'stripe_publishable' => Configuration::get(self::PUBLISHABLE, null, $shopGroupId, $shopId),
            'stripe_test_publishable' => Configuration::get(self::TEST_PUBLISHABLE, null, $shopGroupId, $shopId),
            'stripe_test_key' => Configuration::get(self::TEST_KEY, null, $shopGroupId, $shopId),
            'postcode' => Configuration::get(self::POSTCODE, null, $shopGroupId, $shopId),
            'cardholdername' => Configuration::get(self::CARDHOLDERNAME, null, $shopGroupId, $shopId),
            'reinsurance' => Configuration::get(self::REINSURANCE, null, $shopGroupId, $shopId),
            'visa' => Configuration::get(self::VISA, null, $shopGroupId, $shopId),
            'mastercard' => Configuration::get(self::MASTERCARD, null, $shopGroupId, $shopId),
            'american_express' => Configuration::get(self::AMERICAN_EXPRESS), null, $shopGroupId, $shopId,
            'cb' => Configuration::get(self::CB, null, $shopGroupId, $shopId),
            'diners_club' => Configuration::get(self::DINERS_CLUB, null, $shopGroupId, $shopId),
            'union_pay' => Configuration::get(self::UNION_PAY, null, $shopGroupId, $shopId),
            'jcb' => Configuration::get(self::JCB, null, $shopGroupId, $shopId),
            'discovers' => Configuration::get(self::DISCOVERS, null, $shopGroupId, $shopId),
            'ideal' => Configuration::get(self::ENABLE_IDEAL, null, $shopGroupId, $shopId),
            'sofort' => Configuration::get(self::ENABLE_SOFORT, null, $shopGroupId, $shopId),
            'giropay' => Configuration::get(self::ENABLE_GIROPAY, null, $shopGroupId, $shopId),
            'bancontact' => Configuration::get(self::ENABLE_BANCONTACT, null, $shopGroupId, $shopId),
            'fpx' => Configuration::get(self::ENABLE_FPX, null, $shopGroupId, $shopId),
            'eps' => Configuration::get(self::ENABLE_EPS, null, $shopGroupId, $shopId),
            'p24' => Configuration::get(self::ENABLE_P24, null, $shopGroupId, $shopId),
            'sepa_debit' => Configuration::get(self::ENABLE_SEPA), null, $shopGroupId, $shopId,
            'alipay' => Configuration::get(self::ENABLE_ALIPAY, null, $shopGroupId, $shopId),
            'oxxo' => Configuration::get(self::ENABLE_OXXO, null, $shopGroupId, $shopId),
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY, null, $shopGroupId, $shopId),
            'url_webhhoks' => self::getWebhookUrl(),
        ]);
    }

    /*
     ** @Method: copyAppleDomainFile
     ** @description: Copy apple-developer-merchantid-domain-association file to .well-known/ folder
     **
     ** @arg: (none)
     ** @return: bool
     */
    public function copyAppleDomainFile()
    {
        if (!Tools::copy(_PS_MODULE_DIR_ . 'stripe_official/apple-developer-merchantid-domain-association', _PS_ROOT_DIR_ . '/.well-known/apple-developer-merchantid-domain-association')) {
            return false;
        } else {
            return true;
        }
    }

    /*
     ** @Method: displaySomething
     ** @description: Register Apple Pay domain in Stripe dashboard
     **
     ** @arg: secret_key
     ** @return: (none)
     */
    public function addAppleDomainAssociation($secret_key)
    {
        if (!is_dir(_PS_ROOT_DIR_ . '/.well-known')) {
            if (!mkdir(_PS_ROOT_DIR_ . '/.well-known')) {
                $this->warning[] = $this->l('Settings updated successfully.');

                return false;
            }
        }

        $domain_file = _PS_ROOT_DIR_ . '/.well-known/apple-developer-merchantid-domain-association';
        if (!file_exists($domain_file)) {
            if (!$this->copyAppleDomainFile()) {
                $this->warning[] = $this->l('Your host does not authorize us to add your domain to use ApplePay. To add your domain manually please follow the subject "Add my domain ApplePay manually from my dashboard" which is located in the tab F.A.Q of the module.');
            } else {
                try {
                    \Stripe\Stripe::setApiKey($secret_key);
                    \Stripe\ApplePayDomain::create([
                        'domain_name' => $this->context->shop->domain,
                    ]);

                    $curl = curl_init(Tools::getShopDomainSsl(true, true) . '/.well-known/apple-developer-merchantid-domain-association');
                    curl_setopt($curl, CURLOPT_FAILONERROR, true);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $result = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    if ($httpcode != 200 || !$result) {
                        $this->warning[] = $this->l('The configurations has been saved, however your host does not authorize us to add your domain to use ApplePay. To add your domain manually please follow the subject "Add my domain ApplePay manually from my dashboard in order to use ApplePay" which is located in the tab F.A.Q of the module.');
                    }
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->warning[] = $e->getMessage();
                }
            }
        }
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
            $return_url = urlencode($domain . $_SERVER['REQUEST_URI']);
        }

        $this->context->smarty->assign('return_url', $return_url);
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
            $query = new DbQuery();
            $query->select('*');
            $query->from('stripe_payment');
            $query->where('id_stripe = "' . pSQL($refund_id) . '"');
            $refund = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query->build());
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
                    'UPDATE `' . _DB_PREFIX_ . 'stripe_payment` SET `result` = 2, `date_add` = NOW(), `refund` = "'
                    . pSQL($refund[0]['amount']) . '" WHERE `id_stripe` = "' . pSQL($refund_id) . '"'
                );
            } else { /* Partial refund */
                if (!$this->isZeroDecimalCurrency($currency)) {
                    $ref_amount = $amount * 100;
                }
                try {
                    $ch = \Stripe\Charge::retrieve($refund_id);
                    $ch->refunds->create(['amount' => isset($ref_amount) ? $ref_amount : 0]);
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
                        'UPDATE `' . _DB_PREFIX_ . 'stripe_payment`
                        SET `result` = ' . (int) $result . ',
                            `date_add` = NOW(),
                            `refund` = "' . pSQL($amount) . '"
                        WHERE `id_stripe` = "' . pSQL($refund_id) . '"'
                    );
                }
            }

            $id_order = Order::getOrderByCartId($id_card);
            $order = new Order($id_order);

            $query = new DbQuery();
            $query->select('result');
            $query->from('stripe_payment');
            $query->where('id_stripe = "' . pSQL($refund_id) . '"');
            $state = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

            $this->success = $this->l('Refunds processed successfully');
        } else {
            $this->errors[] = $this->l('Invalid Stripe credentials, please check your configuration.');
        }
    }

    public function isZeroDecimalCurrency($currency)
    {
        // @see: https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
        $zeroDecimalCurrencies = [
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
            'XPF',
        ];

        return in_array(Tools::strtoupper($currency), $zeroDecimalCurrencies);
    }

    /**
     * Get a list of files contained in directory
     *
     * @param string $dir Target directory path
     * @param string $regex Apply regex
     * @param false $onlyFilename Get only filename
     * @param array $results Results search
     *
     * @return array
     */
    private static function getDirContentFiles($dir, $regex = '/.*/', $onlyFilename = false, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path) && preg_match($regex, $value)) {
                $results[] = $onlyFilename ? $value : $path;
            } elseif (is_dir($path) && $value != '.' && $value != '..') {
                self::getDirContentFiles($path, $regex, $onlyFilename, $results);
            }
        }

        return $results;
    }

    /**
     * clean cache for upgrader to prevent issue during module upgrade
     *
     * @return void
     */
    public function cleanModuleCache()
    {
        $path = _PS_MODULE_DIR_ . 'stripe_official/views/templates';
        $regPattern = '/.*\.tpl/';
        $templates = self::getDirContentFiles($path, $regPattern, true);

        foreach ($templates as $tpl) {
            $this->_clearCache($tpl);
        }
    }

    /**
     * get webhook url of stripe_official module
     *
     * @return string
     */
    public static function getWebhookUrl()
    {
        $context = Context::getContext();
        $id_lang = self::getLangIdContext();
        $id_shop = self::getShopIdContext();

        return $context->link->getModuleLink(
            'stripe_official',
            'webhook',
            [],
            true,
            $id_lang,
            $id_shop
        );
    }

    /**
     * get current LangId according to activate multishop feature
     *
     * @return int|null
     */
    public static function getLangIdContext()
    {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && Shop::getContext() === Shop::CONTEXT_ALL) {
            return Configuration::get('PS_LANG_DEFAULT', null, 1, 1);
        }

        return Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * get current ShopId according to activate multishop feature
     *
     * @return int|null
     */
    public static function getShopIdContext()
    {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            return Context::getContext()->shop->id;
        }

        return Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * get current ShopGroupId according to activate multishop feature
     *
     * @return int|null
     */
    public static function getShopGroupIdContext()
    {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            return Context::getContext()->shop->id_shop_group;
        }

        return Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * get Secret Key according MODE staging or live
     *
     * @param null $id_shop Optional, if set, get the secret key of the specified shop
     *
     * @return string
     */
    public function getSecretKey($id_shop_group = null, $id_shop = null)
    {
        $shopGroupId = $id_shop_group ?: Stripe_official::getShopGroupIdContext();
        $shopId = $id_shop ?: Stripe_official::getShopIdContext();
        if (Configuration::get(self::MODE, null, $shopGroupId, $shopId)) {
            return Configuration::get(self::TEST_KEY, null, $shopGroupId, $shopId);
        } else {
            return Configuration::get(self::KEY, null, $shopGroupId, $shopId);
        }
    }

    /**
     * get Publishable Key according MODE staging or live
     *
     * @return string
     */
    public function getPublishableKey($id_shop_group = null, $id_shop = null)
    {
        $shopGroupId = $id_shop_group ?: Stripe_official::getShopGroupIdContext();
        $shopId = $id_shop ?: Stripe_official::getShopIdContext();
        if (Configuration::get(self::MODE, null, $shopGroupId, $shopId)) {
            return Configuration::get(self::TEST_PUBLISHABLE, null, $shopGroupId, $shopId);
        } else {
            return Configuration::get(self::PUBLISHABLE, null, $shopGroupId, $shopId);
        }
    }

    public function checkApiConnection($secretKey = null)
    {
        if (!$secretKey) {
            $secretKey = $this->getSecretKey();
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);

            return \Stripe\Account::retrieve();
        } catch (Exception $e) {
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::openLogger();
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError($e->getMessage());
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();
            $this->errors[] = $e->getMessage();

            return false;
        }
    }

    public function updateConfigurationKey($oldKey, $newKey)
    {
        if (Configuration::hasKey($oldKey)) {
            $set = '';

            if ($oldKey == '_PS_STRIPE_secure' && Configuration::get($oldKey) == '0') {
                $set = ',`value` = 2';
            }

            $sql = 'UPDATE `' . _DB_PREFIX_ . 'configuration`
                    SET `name`="' . pSQL($newKey) . '"' . $set . '
                    WHERE `name`="' . pSQL($oldKey) . '"';

            return Db::getInstance()->execute($sql);
        }
    }

    public function getPaymentMethods()
    {
        $configurations = Configuration::getMultiple([
            self::VISA,
            self::MASTERCARD,
            self::AMERICAN_EXPRESS,
            self::CB,
            self::DINERS_CLUB,
            self::UNION_PAY,
            self::JCB,
            self::DISCOVERS,
        ]);

        $results = [];
        foreach ($configurations as $key => $configuration) {
            if ($configuration === 'on') {
                $results[] = ['name' => Tools::strtolower(str_replace('STRIPE_PAYMENT_', '', $key))];
            }
        }

        return $results;
    }

    public function captureFunds($amount, $id_payment_intent)
    {
        \Stripe\Stripe::setApiKey($this->getSecretKey());

        try {
            $intent = \Stripe\PaymentIntent::retrieve($id_payment_intent);
            $intent->capture(['amount_to_capture' => $amount]);

            return true;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                'Fail to capture amount. Stripe SDK return error: ' . $e
            );
            Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

            return false;
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
            Media::addJsDef([
                'transaction_refresh_url' => $this->context->link->getAdminLink(
                    'AdminAjaxTransaction',
                    true,
                    [],
                    ['ajax' => 1, 'action' => 'refresh']
                ),
            ]);
        }
    }

    /**
     * Add a tab to controle intents on a cart details admin page
     */
    public function hookDisplayAdminCartsView($params)
    {
        $stripePayment = new StripePayment();
        $paymentInformations = $stripePayment->getStripePaymentByCart($params['cart']->id);

        if (empty($paymentInformations->getIdPaymentIntent())) {
            return;
        }

        $paymentInformations->state = $paymentInformations->state ? 'TEST' : 'LIVE';
        $paymentInformations->url_dashboard = $stripePayment->getDashboardUrl();

        $this->context->smarty->assign([
            'paymentInformations' => $paymentInformations,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/admin_cart.tpl');
    }

    /**
     * Add a tab to controle intents on an order details admin page (tab header)
     *
     * @return html
     */
    public function hookDisplayAdminOrderTabOrder($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.4', '>=')) {
            $order = new Order($params['id_order']);
        } else {
            $order = $params['order'];
        }

        if ($order->module != 'stripe_official') {
            return;
        }

        return $this->display(__FILE__, 'views/templates/hook/admin_tab_order.tpl');
    }

    public function hookDisplayAdminOrderTabLink($params)
    {
        return $this->hookDisplayAdminOrderTabOrder($params);
    }

    /**
     * Add a tab to controle intents on an order details admin page (tab content)
     *
     * @return html
     */
    public function hookDisplayAdminOrderContentOrder($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.4', '>=')) {
            $order = new Order($params['id_order']);
        } else {
            $order = $params['order'];
        }

        $stripePayment = new StripePayment();
        $stripePayment->getStripePaymentByCart($order->id_cart);

        $stripeCapture = new StripeCapture();
        $stripeCapture->getByIdPaymentIntent($stripePayment->getIdPaymentIntent());

        $dispute = false;
        if (!empty($stripePayment->getIdStripe())) {
            $stripeDispute = new StripeDispute();
            $dispute = $stripeDispute->orderHasDispute($stripePayment->getIdStripe(), $order->id_shop);
        }

        $this->context->smarty->assign([
            'stripe_charge' => $stripePayment->getIdStripe(),
            'stripe_paymentIntent' => $stripePayment->getIdPaymentIntent(),
            'stripe_date' => $stripePayment->getDateAdd(),
            'stripe_dashboardUrl' => $stripePayment->getDashboardUrl(),
            'stripe_paymentType' => $stripePayment->getType(),
            'stripe_dateCatch' => $stripeCapture->getDateCatch(),
            'stripe_dateAuthorize' => $stripeCapture->getDateAuthorize(),
            'stripe_expired' => $stripeCapture->getExpired(),
            'stripe_dispute' => $dispute,
            'stripe_voucher_expire' => $stripePayment->getVoucherExpire(),
            'stripe_voucher_validate' => $stripePayment->getVoucherValidate(),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/admin_content_order.tpl');
    }

    public function hookDisplayAdminOrderTabContent($params)
    {
        return $this->hookDisplayAdminOrderContentOrder($params);
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $order = new Order($params['id_order']);

        if ($order->module == 'stripe_official'
            && !empty($order->getHistory($this->context->language->id, Configuration::get(self::CAPTURE_WAITING)))
            && in_array($params['newOrderStatus']->id, explode(',', Configuration::get(self::CAPTURE_STATUS)))) {
            $stripePayment = new StripePayment();

            try {
                $stripePaymentDatas = $stripePayment->getStripePaymentByCart($order->id_cart);
                $amount = $this->isZeroDecimalCurrency($stripePayment->currency) ? $order->total_paid : $order->total_paid * 100;

                if (!$this->captureFunds($amount, $stripePaymentDatas->id_payment_intent)) {
                    return false;
                }

                $stripeCapture = new StripeCapture();
                $stripeCapture->getByIdPaymentIntent($stripePaymentDatas->id_payment_intent);
                $stripeCapture->date_authorize = date('Y-m-d H:i:s');
                $stripeCapture->save();
            } catch (\Stripe\Exception\UnexpectedValueException $e) {
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                    $e->getMessage(),
                    null,
                    null,
                    'Stripe_official - hookActionOrderStatusUpdate'
                );
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

                return false;
            } catch (PrestaShopException $e) {
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::logError(
                    $e->getMessage(),
                    null,
                    null,
                    'Stripe_official - hookActionOrderStatusUpdate'
                );
                Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler::closeLogger();

                return false;
            }
        }

        return true;
    }

    /**
     * Load JS on the front office order page
     */
    public function hookHeader()
    {
        $orderPageNames = ['order', 'orderopc'];
        Hook::exec('actionStripeDefineOrderPageNames', ['orderPageNames' => &$orderPageNames]);
        if (!in_array(Dispatcher::getInstance()->getController(), $orderPageNames)) {
            return;
        }

        if (!self::isWellConfigured() || !$this->active) {
            return;
        }

        $cart = $this->context->cart;

        $address = new Address($cart->id_address_invoice);
        $currency = new Currency($cart->id_currency);
        $amount = $cart->getOrderTotal();
        $amount = Tools::ps_round($amount, 2);
        $amount = $this->isZeroDecimalCurrency($currency->iso_code) ? $amount : $amount * 100;

        if ($amount == 0) {
            return;
        }

        // Merchant country (for payment request API)
        $merchantCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->context->controller->registerJavascript(
                $this->name . '-stripe-v3',
                'https://js.stripe.com/v3/',
                [
                    'server' => 'remote',
                    'position' => 'head',
                ]
            );
            $this->context->controller->registerJavascript(
                $this->name . '-payments',
                'modules/' . $this->name . '/views/js/payments.js'
            );

            if (Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY)) {
                $this->context->controller->registerJavascript(
                    $this->name . '-stripepaymentrequest',
                    'modules/' . $this->name . '/views/js/payment_request.js'
                );
            }

            $this->context->controller->registerStylesheet(
                $this->name . '-checkoutcss',
                'modules/' . $this->name . '/views/css/checkout.css'
            );
            $prestashop_version = '1.7';
            $firstname = str_replace('"', '\\"', $this->context->customer->firstname);
            $lastname = str_replace('"', '\\"', $this->context->customer->lastname);
            $stripe_fullname = $firstname . ' ' . $lastname;
        } else {
            $this->context->controller->addJS('https://js.stripe.com/v3/');
            $this->context->controller->addJS($this->_path . '/views/js/payments.js');

            if (Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY)) {
                $this->context->controller->addJS($this->_path . '/views/js/payment_request.js');
            }

            $this->context->controller->addCSS($this->_path . '/views/css/checkout.css', 'all');
            $prestashop_version = '1.6';
            $firstname = str_replace('\'', '\\\'', $this->context->customer->firstname);
            $lastname = str_replace('\'', '\\\'', $this->context->customer->lastname);
            $stripe_fullname = $firstname . ' ' . $lastname;
        }

        $auto_save_card = false;
        if (Configuration::get(self::SAVE_CARD) == 'on' && Configuration::get(self::ASK_CUSTOMER) == '0') {
            $auto_save_card = true;
        }

        // Javacript variables needed by Elements
        Media::addJsDef([
            'stripe_pk' => $this->getPublishableKey(),
            'stripe_merchant_country_code' => $merchantCountry->iso_code,

            'stripe_currency' => Tools::strtolower($currency->iso_code),
            'stripe_amount' => Tools::ps_round($amount, 2),

            'stripe_fullname' => $stripe_fullname,

            'stripe_address' => $address,
            'stripe_address_country_code' => Country::getIsoById($address->id_country),

            'stripe_email' => $this->context->customer->email,

            'stripe_locale' => $this->context->language->iso_code,

            'stripe_auto_save_card' => $auto_save_card,

            'stripe_validation_return_url' => $this->context->link->getModuleLink(
                $this->name,
                'validation',
                [],
                true
            ),

            'stripe_order_confirmation_return_url' => $this->context->link->getModuleLink(
                $this->name,
                'orderConfirmationReturn',
                [],
                true
            ),

            'stripe_create_intent_url' => $this->context->link->getModuleLink(
                $this->name,
                'createIntent',
                [],
                true
            ),

            'stripe_css' => '{"base": {"iconColor": "#666ee8","color": "#31325f","fontWeight": 400,"fontFamily": "-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif","fontSmoothing": "antialiased","fontSize": "15px","::placeholder": { "color": "#aab7c4" },":-webkit-autofill": { "color": "#666ee8" }}}',

            'stripe_ps_version' => $prestashop_version,

            'stripe_postcode_disabled' => Configuration::get(self::POSTCODE),
            'stripe_cardholdername_enabled' => Configuration::get(self::CARDHOLDERNAME),
            'stripe_reinsurance_enabled' => Configuration::get(self::REINSURANCE),
            'stripe_module_dir' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name),

            'stripe_message' => [
                'processing' => $this->l('Processing…'),
                'accept_cgv' => $this->l('Please accept the CGV'),
                'redirecting' => $this->l('Redirecting…'),
            ],
        ]);
    }

    /**
     * Hook Stripe Payment for PS 1.6
     */
    public function hookPayment($params)
    {
        if (!self::isWellConfigured() || !$this->active) {
            return;
        }

        $stripeAccount = $this->checkApiConnection();

        if (empty($stripeAccount) === true) {
            $this->context->smarty->assign([
                'stripeError' => $this->l(
                    'No API keys have been provided. Please contact the owner of the website.',
                    $this->name
                ),
            ]);
        }

        // The hookHeader isn't triggered when updating the cart or the carrier
        // on PS1.6 with OPC; so we need to update the PaymentIntent here
        $cart = $params['cart'];
        $address = new Address($cart->id_address_invoice);
        $currency = new Currency($cart->id_currency);
        $amount = $cart->getOrderTotal();
        $amount = Tools::ps_round($amount, 2);
        $amount = $this->isZeroDecimalCurrency($currency->iso_code) ? $amount : $amount * 100;

        if (Configuration::get(self::REINSURANCE) == null) {
            $stripe_reinsurance_enabled = 'off';
        } else {
            $stripe_reinsurance_enabled = Configuration::get(self::REINSURANCE);
        }

        if (Configuration::get(self::POSTCODE) == null) {
            $stripe_postcode_enabled = 'off';
        } else {
            $stripe_postcode_enabled = Configuration::get(self::POSTCODE);
        }

        $show_save_card = false;
        if (Configuration::get(self::SAVE_CARD) == 'on' && Configuration::get(self::ASK_CUSTOMER) == '1') {
            $show_save_card = true;
        }

        // Send the payment amount, it may have changed
        $this->context->smarty->assign([
            'stripe_amount' => Tools::ps_round($amount, 0),
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY),
            'prestashop_version' => '1.6',
            'stripe_postcode_enabled' => $stripe_postcode_enabled,
            'stripe_cardholdername_enabled' => Configuration::get(self::CARDHOLDERNAME),
            'stripe_reinsurance_enabled' => $stripe_reinsurance_enabled,
            'stripe_payment_methods' => $this->getPaymentMethods(),
            'module_dir' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name),
            'customer_name' => $address->firstname . ' ' . $address->lastname,
            'stripe_save_card' => Configuration::get(self::SAVE_CARD),
            'show_save_card' => $show_save_card,
        ]);

        // Fetch country based on invoice address and currency
        $country = Country::getIsoById($address->id_country);

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
            $currency_iso_code = Tools::strtolower($currency->iso_code);
            if (isset($paymentMethod['currencies']) && !in_array($currency_iso_code, $paymentMethod['currencies'])) {
                continue;
            }

            $display .= $this->display(__FILE__, 'views/templates/front/payment_form_' . basename($name) . '.tpl');
        }
        if ($display != '') {
            $display .= $this->display(__FILE__, 'views/templates/front/payment_form_common.tpl');
        }

        $stripeCustomer = new StripeCustomer();
        $stripeCustomer->getCustomerById($this->context->customer->id, $stripeAccount->id);

        if ($stripeCustomer->id == null) {
            return $display;
        }

        $stripeCustomerExists = $stripeCustomer->stripeCustomerExists(
            $this->context->customer->email,
            $stripeCustomer->stripe_customer_key
        );
        if ($stripeCustomerExists === false) {
            return $display;
        }

        $customerCards = $stripeCustomer->getStripeCustomerCards();

        if (empty($customerCards)) {
            return $display;
        }

        foreach ($customerCards as $card) {
            if ($card->card->exp_month < date('m') && $card->card->exp_year <= date('Y')) {
                continue;
            }

            $this->context->smarty->assign([
                'id_payment_method' => $card->id,
                'last4' => $card->card->last4,
                'brand' => Tools::ucfirst($card->card->brand),
            ]);

            $display .= $this->display(__FILE__, 'views/templates/front/payment_form_save_card.tpl');
        }

        return $display;
    }

    public function hookDisplayPaymentEU($params)
    {
        if (!self::isWellConfigured() || !$this->active || version_compare(_PS_VERSION_, '1.7', '>=')) {
            return [];
        }

        $payment = $this->hookPayment($params);

        Media::addJsDef([
            'stripe_compliance' => true,
        ]);

        $payment_options = [
            'cta_text' => $this->l('Pay by card'),
            'logo' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/logo.png'),
            'form' => $payment,
        ];

        return $payment_options;
    }

    /**
     * Hook Stripe Payment for PS 1.7
     */
    public function hookPaymentOptions($params)
    {
        if (!self::isWellConfigured() || !$this->active) {
            return;
        }

        $stripeAccount = $this->checkApiConnection();

        if (empty($stripeAccount) === true) {
            $this->context->smarty->assign([
                'stripeError' => $this->l(
                    'No API keys have been provided. Please contact the owner of the website.',
                    $this->name
                ),
            ]);
        }

        $address = new Address($params['cart']->id_address_invoice);

        if (Configuration::get(self::POSTCODE) == null) {
            $stripe_reinsurance_enabled = 'off';
        } else {
            $stripe_reinsurance_enabled = Configuration::get(self::POSTCODE);
        }

        $show_save_card = false;
        if (Configuration::get(self::SAVE_CARD) == 'on' && Configuration::get(self::ASK_CUSTOMER) == '1') {
            $show_save_card = true;
        }

        $this->context->smarty->assign([
            'applepay_googlepay' => Configuration::get(self::ENABLE_APPLEPAY_GOOGLEPAY),
            'prestashop_version' => '1.7',
            'publishableKey' => $this->getPublishableKey(),
            'stripe_postcode_enabled' => $stripe_reinsurance_enabled,
            'stripe_cardholdername_enabled' => Configuration::get(self::CARDHOLDERNAME),
            'stripe_reinsurance_enabled' => Configuration::get(self::REINSURANCE),
            'stripe_payment_methods' => $this->getPaymentMethods(),
            'module_dir' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name),
            'customer_name' => $address->firstname . ' ' . $address->lastname,
            'stripe_save_card' => Configuration::get(self::SAVE_CARD),
            'show_save_card' => $show_save_card,
        ]);

        // Fetch country based on invoice address and currency
        $address = new Address($params['cart']->id_address_invoice);
        $country = Country::getIsoById($address->id_country);
        $currency = Tools::strtolower($this->context->currency->iso_code);

        // Show only the payment methods that are relevant to the selected country and currency
        $options = [];
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
                ->setCallToActionText($this->button_label[$name]);

            // Display additional information for redirect and receiver based payment methods
            if (in_array($paymentMethod['flow'], ['redirect', 'receiver'])) {
                $option->setAdditionalInformation(
                    $this->context->smarty->fetch(
                        'module:' . $this->name . '/views/templates/front/payment_info_' . basename($paymentMethod['flow']) . '.tpl'
                    )
                );
            }

            // Payment methods with embedded form fields
            $option->setForm(
                $this->context->smarty->fetch(
                    'module:' . $this->name . '/views/templates/front/payment_form_' . basename($name) . '.tpl'
                )
            );

            $options[] = $option;
        }

        $stripeCustomer = new StripeCustomer();
        $stripeCustomer->getCustomerById($this->context->customer->id, $stripeAccount->id);

        if ($stripeCustomer->id == null) {
            return $options;
        }

        $stripeCustomerExists = $stripeCustomer->stripeCustomerExists(
            $this->context->customer->email,
            $stripeCustomer->stripe_customer_key
        );
        if ($stripeCustomerExists === false) {
            return $options;
        }

        $customerCards = $stripeCustomer->getStripeCustomerCards();

        if (empty($customerCards)) {
            return $options;
        }

        foreach ($customerCards as $card) {
            if ($card->card->exp_month < date('m') && $card->card->exp_year <= date('Y')) {
                continue;
            }

            $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $option
                ->setModuleName($this->name)
                ->setCallToActionText($this->button_label['save_card'] . ' : ' . Tools::ucfirst($card->card->brand) . ' **** **** **** ' . $card->card->last4);

            $this->context->smarty->assign([
                'id_payment_method' => $card->id,
            ]);

            $option->setForm(
                $this->context->smarty->fetch(
                    'module:' . $this->name . '/views/templates/front/payment_form_save_card.tpl'
                )
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
            $prestashop_version = '1.7';
        } else {
            $order = $params['objOrder'];
            $prestashop_version = '1.6';
        }

        if (!self::isWellConfigured() || !$this->active || $order->module != $this->name) {
            return;
        }

        $stripePayment = new StripePayment();
        $stripePayment->getStripePaymentByCart($order->id_cart);

        $this->context->smarty->assign([
            'stripe_order_reference' => pSQL($order->reference),
            'prestashop_version' => $prestashop_version,
            'stripePayment' => $stripePayment,
        ]);

        return $this->display(__FILE__, 'views/templates/front/order-confirmation.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $prestashop_version = '1.7';
        } else {
            $prestashop_version = '1.6';
        }

        $shopGroupId = Stripe_official::getShopGroupIdContext();
        $shopId = Stripe_official::getShopIdContext();

        $this->context->smarty->assign([
            'prestashop_version' => $prestashop_version,
            'isSaveCard' => Configuration::get(self::SAVE_CARD, null, $shopGroupId, $shopId),
        ]);

        return $this->display(__FILE__, 'my-account-stripe-cards.tpl');
    }
}
