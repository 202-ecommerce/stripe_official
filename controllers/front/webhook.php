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

require_once dirname(__FILE__).'/../../libraries/sdk/stripe/init.php';

class stripe_officialWebhookModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {
        if (Configuration::get('_PS_STRIPE_mode') == 1) {
            $secret_key = Configuration::get('_PS_STRIPE_test_key');
        } else {
            $secret_key = Configuration::get('_PS_STRIPE_key');
        }

        try {
            \Stripe\Stripe::setApiKey($secret_key);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
        // Retrieve the request's body and parse it as JSON
        $input = @Tools::file_get_contents("php://input");
        $event_json = json_decode($input);
        try {
            \Stripe\Event::retrieve($event_json->id);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }

        http_response_code(200);

        if ($event_json) {
            if ($event_json->data->object->metadata->verification_url != Configuration::get('PS_SHOP_DOMAIN')) {
                die('This order have been done on other site');
            }
            if ($event_json->type == "charge.canceled" || $event_json->type == "charge.failed") {
                $payment_type = $event_json->data->object->source->type;
                $id_payment = $event_json->data->object->id;
                if ($payment_type == 'sofort') {
                    $stripe_payment = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'stripe_payment WHERE `id_stripe` = "' . pSQL($id_payment) . '"');
                    if ($stripe_payment) {
                        $id_order = Order::getOrderByCartId($stripe_payment['id_cart']);
                        $order = new Order($id_order);
                        if (Validate::isLoadedObject($order)) {
                            $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                        }
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = 0 WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                    }
                }
            }
            if ($event_json->type == "charge.succeeded") {
                $payment_type = $event_json->data->object->source->type;
                $id_payment = $event_json->data->object->id;
                if ($payment_type == 'sofort') {
                    $stripe_payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                    if ($stripe_payment['result'] == Stripe_official::_PENDING_SOFORT_) {
                        $id_order = Order::getOrderByCartId($stripe_payment['id_cart']);
                        $order = new Order($id_order);
                        if (Validate::isLoadedObject($order)) {
                            $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                        }
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = 1 WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                    } else {
                        die('Payment is not in state pending');
                    }
                }
            }
            if ($event_json->type == "source.chargeable") {
                $payment_type = $event_json->data->object->type;
                if (in_array($payment_type, array('ideal', 'bancontact', 'giropay', 'sofort'))) {
                    $source = \Stripe\Source::retrieve($event_json->data->object->id);
                    $stripe = Module::getInstanceByName('stripe_official');
                    if ($source->status != "chargeable") {
                        die($stripe->l('Source is not in state chargeable'));
                    }
                    $cart_id = $event_json->data->object->metadata->cart_id;
                    $count = 0;
                    $found = false;
                    while ($count < 10) {
                        $id_order = Order::getOrderByCartId($cart_id);
                        if ($id_order) {
                            $found = true;
                            break;
                        }
                        $count++;
                        usleep(500000);
                    }
                    if (!$found) {
                        $params = array(
                            'token' => $event_json->data->object->id,
                            'amount' => $event_json->data->object->amount,
                            'currency' => $event_json->data->object->currency,
                            'cardHolderName' => $event_json->data->object->owner->name,
                            'cart_id' => $cart_id,
                            'carHolderEmail' => $event_json->data->object->metadata->email,
                            'type' => $event_json->data->object->type,
                        );
                        $stripe->chargeWebhook($params);
                    } else {
                        die($stripe->l('Order is already created'));
                    }
                }
            }
            die('ok');
        }
    }
}
