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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

use Stripe_officialClasslib\Actions\ActionsHandler;

class stripe_officialValidationModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ssl = true;
        $this->ajax = true;
        $this->json = true;
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        // Create the handler
        $handler = new ActionsHandler();

         // Set input data
        $handler->setConveyor(array(
                    'source' => Tools::getValue('source'),
                    'response' => Tools::getValue('response'),
                    'module' => $this->module,
                    'context' => $this->context,
                ));

        // Set list of actions to execute
        if (empty(Tools::getValue('source'))) {
            $handler->addActions('prepareFlowNone', 'updatePaymentIntent', 'createOrder', 'addTentative');
        } else {
            $handler->addActions('prepareFlowRedirect', 'updatePaymentIntent', 'createOrder', 'addTentative');
        }

        // Process actions chain
        if ($handler->process('ValidationOrder')) {
            // Retrieve and use resulting data
            $returnValues = $handler->getConveyor();
        } else {
            // Handle error
            ProcessLoggerHandler::logError('Order validation process failed.');
        }


        $id_order = Order::getOrderByCartId($this->context->cart->id);

        $url = Context::getContext()->link->getPageLink(
            'order-confirmation',
            true,
            null,
            array(
                'id_cart' => (int)$this->context->cart->id,
                'id_module' => (int)$this->module->id,
                'id_order' => (int)$id_order,
                'key' => $returnValues['secure_key']
            )
        );

        if (!empty(Tools::getValue('source'))) {
            Tools::redirect($url);
            exit;
        }

         /* Ajax redirection Order Confirmation */
        $chargeResult = array(
            'code' => '1',
            'url' => $url
        );
        $this->ajaxDie(Tools::jsonEncode($chargeResult));
    }
}