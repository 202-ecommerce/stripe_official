<?php
/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */



class stripe_officialstripeWebhookModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {

        if (Tools::getValue('_PS_STRIPE_mode') == 1) {
            $secret_key = Configuration::get('_PS_STRIPE_test_key');
        } else {
            $secret_key = Configuration::get('_PS_STRIPE_key');
        }
        \Stripe\Stripe::setApiKey($secret_key);

        $input = @file_get_contents("php://input");
        $event_json = json_decode($input);



        // Retrieve the request's body and parse it as JSON

        $file = fopen('log.txt', "w+");
        fwrite($file, print_r($event_json, true).'\n');
        fclose($file);
        http_response_code(200);
        if ($event_json) {
            //TODO: check events charge.succeeded or charge.failed Sofort

            if ($event_json->type == "charge.canceled" || $event_json->type == "charge.failed") {
                $payment_type = $event_json->data->object->source->type;
                $id_payment = $event_json->data->object->id;
                $stripe_payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                if ($stripe_payment) {
                    $id_order = Order::getOrderByCartId($stripe_payment['id_cart']);
                    $order = new Order($id_order);
                    if (Validate::isLoadedObject($order)) {
                        $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                    }
                }
            }
            if ($event_json->type == "charge.succeeded") {
                $payment_type = $event_json->data->object->source->type;
                $id_payment = $event_json->data->object->id;
                if ($payment_type == 'sofort') {
                    $stripe_payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                    if ($stripe_payment['result'] == 0) {
                        $id_order = Order::getOrderByCartId($stripe_payment['id_cart']);
                        $order = new Order($id_order);
                        $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = 1 WHERE `id_stripe` = "'.pSQL($id_payment).'"');
                    }
                }
            }
            if ($event_json->type == "source.chargeable") {
                $payment_type = $event_json->data->object->type;
                if (in_array($payment_type, array('ideal', 'bancontact', 'giropay', 'sofort'))) {
                    // TODO: create charge and commande
                   /* $stripe = Module::getInstanceByName('stripe_official');
                    $params = array(
                        'token' => $event_json->data->object->id,
                        'amount' => $event_json->data->object->amount,
                        'currency' => $event_json->data->object->currency,
                        'cardHolderName' => $event_json->data->object->owner->name,
                        'type' => $payment_type,
                    );
                    $stripe->chargev2($params);*/
                }

            }

        }



    }


}




