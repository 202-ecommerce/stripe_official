<?php
/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class stripe_officialpaymentRequestModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    private $stripe;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->stripe = Module::getInstanceByName('stripe_official');
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        StripeLogger::logInfo('************************** START PaymentRequest **************************');

        $handler = StripePaymentRequestHandler::getInstance();
        try {
            $handler->changeCartProducts()
                ->changeCartAddress()
                ->loadShippingOptions()
                ->changeCartCarrier()
                ->changeCustomer();
        } catch (StripePaymentRequestException $e) {
            StripeLogger::logInfo($e->getMessage());
            StripeLogger::logInfo('StripePaymentRequestException: '.$e->getMessage());
            $this->renderAjax(array('status' => 'fail', 'error' => $e->getMessage()));
        }

        if (Tools::getValue('shippingoptionchange') == true || Tools::getValue('shippingaddresschange') == true) {
            StripeLogger::logInfo('PaymentRequest: shippingoptionchange shippingaddresschange');
            $response['status'] = 'success';
        }
        $response['shippingOptions'] = $handler->getShippingOptions();
        $response['total']['label'] = $handler->getLabel();
        $response['total']['amount'] = $handler->getAmount();

        $handler->close();

        $this->renderAjax($response);
    }

    protected function renderAjax($response)
    {
        $json = Tools::jsonEncode($response);
        StripeLogger::logInfo('************************** END PaymentRequest **************************');
        $this->ajaxDie($json);
    }
}
