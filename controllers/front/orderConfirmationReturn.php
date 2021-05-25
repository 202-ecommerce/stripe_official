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

use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialOrderConfirmationReturnModuleFrontController extends ModuleFrontController
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

        $paymentIntent = Tools::getValue('paymentIntent');

        $stripeIdempotencyKey = new StripeIdempotencyKey();
        $stripeIdempotencyKey->getByIdPaymentIntent($paymentIntent);

        $id_order = Order::getOrderByCartId($stripeIdempotencyKey->id_cart);

        if ($id_order == NULL) {
            echo Tools::jsonEncode('retry');
            exit;
        }

        if (isset($this->context->customer->secure_key)) {
            $secure_key = $this->context->customer->secure_key;
        } else {
            $secure_key = false;
        }

        $url = Context::getContext()->link->getPageLink(
            'order-confirmation',
            true,
            null,
            array(
                'id_cart' => (int)$stripeIdempotencyKey->id_cart,
                'id_module' => (int)$this->module->id,
                'id_order' => (int)$id_order,
                'key' => $secure_key
            )
        );

        echo Tools::jsonEncode($url);
        exit;
    }
}
