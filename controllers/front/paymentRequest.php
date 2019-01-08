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

class Stripe_officialPaymentRequestModuleFrontController extends ModuleFrontController
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

        if (Tools::getValue('shippingoptionchange') == false && Tools::getValue('shippingaddresschange') == false && Tools::getValue('onToken') == false) {
            $cart = new Cart();
            $cart->id_carrier = Tools::getValue('carrier');
            $cart->id_lang = (int)$this->context->cookie->id_lang;
            $cart->id_currency = (int)$this->context->cookie->id_currency;
            $cart->id_guest = (int)$this->context->cookie->id_guest;
            $cart->id_shop_group = (int)$this->context->shop->id_shop_group;
            $cart->id_shop = $this->context->shop->id;
            $cart->id_customer = Tools::getValue('idCustomer');
            $cart->save();

            if (isset($this->context->cookie->id_cart)) {
                $this->context->cookie->__unset('id_cart');
            }
            $this->context->cookie->__set('id_cart', $cart->id);
            $this->context->cookie->write();

            $this->context->cart = $cart;

            $id_product = Tools::getValue('product');
            $id_product_combination = Tools::getValue('productCombination');
            $quantity = Tools::getValue('quantity');
            $this->addProductToCart($id_product, $id_product_combination, $cart->id, $quantity);
        } elseif (Tools::getValue('shippingoptionchange') == true) {
            $this->changeCartCarrier(Tools::getValue('carrier'));
        } elseif (Tools::getValue('shippingaddresschange') == true) {
            $this->changeCartAddress(Tools::getValue('address'));
        } elseif (Tools::getValue('onToken') == true) {
            $this->changeCartCarrier(Tools::getValue('carrierInfos'));
            $this->changeCartAddress(Tools::getValue('addressInfos'));
            if ($this->context->cart->id_customer == 0) {
                $this->createGuest(Tools::getValue('payerEmail'), Tools::getValue('payerName'));
            }
        }

        $currency = $this->context->currency->iso_code;
        $amount = $this->context->cart->getOrderTotal(true, Cart::BOTH, null, $this->context->cart->id_carrier);
        $amount = $this->isZeroDecimalCurrency($currency) ? $amount : $amount * 100;

        echo $amount;
        die;
    }

    private function createGuest($email, $name)
    {
        $fullname = explode(' ', $name);
        $firstname = $fullname[0];
        $lastname = $fullname[1];

        $customer = new Customer();
        $customer->id_shop_group = (int)$this->context->shop->id_shop_group;
        $customer->id_shop = $this->context->shop->id;
        $customer->id_gender = 0;
        $customer->id_default_group = 2;
        $customer->id_lang = (int)$this->context->cookie->id_lang;
        $customer->id_risk = 0;
        $customer->firstname = $firstname;
        $customer->lastname = $lastname;
        $customer->email = $email;
        $customer->passwd = md5(Tools::passwdGen(8, 'RANDOM'));
        $customer->newsletter = 0;
        $customer->optin = 0;
        $customer->active = 1;
        $customer->is_guest = 1;
        $customer->save();

        $this->context->cart->id_customer = $customer->id;
        $this->context->cart->save();
    }

    private function changeCartAddress($address)
    {
        if ($address['recipient'] != '') {
            $name = explode(' ', $address['recipient']);
            if (Validate::isName($name[0])) {
                $firstname = $name[0];
                if (isset($name[1]) && $name[1] != '') {
                    $lastname = $name[1];
                } else {
                    $lastname = $name[0];
                }
            } else {
                echo $this->l('Invalide address name');
                die;
            }
        } else {
            $firstname = "tmpname";
            $lastname = "tmpname";
        }

        $city = $address['city'];
        $country = $address['country'];

        if (isset($address['addressLine'][0]) && $address['addressLine'][0] != '') {
            $line1 = $address['addressLine'][0];
            if (isset($address['addressLine'][1]) && $address['addressLine'][1] != '') {
                $line2 = $address['addressLine'][1];
            } else {
                $line2 = '';
            }
        } else {
            echo $this->l('Missing address location');
            die;
        }

        $postal_code = $address['postalCode'];
        $phone = $address['phone'];

        $id_address = $this->addressExists($postal_code, $city, $phone);

        if (!$id_address) {
            $newAddress = new Address();
            $newAddress->id_customer = $this->context->customer->id;
            $newAddress->id_country = $this->getAddressCountry($country);
            $newAddress->alias = $line1;
            $newAddress->firstname = $firstname;
            $newAddress->lastname = $lastname;
            $newAddress->address1 = $line1;
            $newAddress->address2 = $line2;
            $newAddress->postcode = $postal_code;
            $newAddress->city = $city;
            $newAddress->phone = $phone;

            try {
                $newAddress->save();
            } catch (Exception $e) {
                echo $this->l('Imcomplete address');
                die;
            }

            $idAddress = $newAddress->id;
        } else {
            $idAddress = $id_address;
        }

        $id_zone = Address::getZoneById($idAddress);
        if (!Address::isCountryActiveById($idAddress) ||
            empty(Carrier::getCarriers($this->context->language->id, true, false, $id_zone))) {
            echo $this->l('Invalide address location');
            die;
        }

        $this->context->cart->id_address_delivery = $idAddress;
        $this->context->cart->id_address_invoice = $idAddress;

        try {
            $this->context->cart->save();
        } catch (Exception $e) {
            echo $this->l('Imcomplete address');
            die;
        }
    }

    private function changeCartCarrier($carrier)
    {
        $this->context->cart = new Cart($this->context->cookie->id_cart);
        $this->context->cart->id_carrier = $carrier;
        $this->context->cart->delivery_option = 'a:1:{i:'.$this->context->cart->id_address_delivery.';s:2:"'.$carrier.',";}';

        $this->context->cart->save();
    }

    private function getAddressCountry($country)
    {
        $sql = "SELECT id_country FROM "._DB_PREFIX_."country
                WHERE iso_code='".pSQL(Tools::strtoupper($country))."'";

        return DB::getInstance()->getValue($sql);
    }

    private function addressExists($postcode, $city, $phone)
    {
        $sql = "SELECT id_address FROM "._DB_PREFIX_."address
                WHERE postcode='".pSQL($postcode)."'
                AND city='".pSQL($city)."'
                AND phone='".pSQL($phone)."'
                AND id_customer='".(int)$this->context->customer->id."'";

        $result = DB::getInstance()->getValue($sql);

        if ($result != '') {
            return $result;
        } else {
            return false;
        }
    }

    private function addProductToCart($id_product, $id_product_combination, $id_cart, $quantity)
    {
        $sql = "INSERT INTO `"._DB_PREFIX_."cart_product`(`id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `quantity`, `date_add`)
                VALUES (".$id_cart.",".$id_product.",0,".$this->context->shop->id.",".$id_product_combination.",".$quantity.",'".date("Y-m-d H:i:s")."')";

        Db::getInstance()->execute($sql);
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
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF',
        );
        return in_array($currency, $zeroDecimalCurrencies);
    }
}
