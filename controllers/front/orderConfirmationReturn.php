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

        if (Tools::getValue('payment_intent')) {
            // for redirect payment methods
            $payment_intent = Tools::getValue('payment_intent');
        } else {
            $payment_intent = Tools::getValue('paymentIntent');
        }

        $intent = \Stripe\PaymentIntent::retrieve(
            $payment_intent
        );

        $payment_method = $intent->payment_method_types[0];

        if (Tools::getValue('redirect_status') == 'failed') {
            $url = Context::getContext()->link->getModuleLink(
                'stripe_official',
                'orderFailure',
                array(),
                true
            );
        } else {
            $datas = array(
                'payment_method' => $payment_method
            );

            if ($payment_method == 'oxxo') {
                $datas['voucher_url'] = $intent->next_action->oxxo_display_details->hosted_voucher_url;
            }

            $url = Context::getContext()->link->getModuleLink(
                'stripe_official',
                'orderSuccess',
                $datas,
                true
            );
        }

        // for redirect payments
        if (Stripe_official::$paymentMethods[$payment_method]['flow'] == 'redirect') {
            Tools::redirect($url);
            exit;
        }

        echo Tools::jsonEncode($url);
        exit;
    }
}
