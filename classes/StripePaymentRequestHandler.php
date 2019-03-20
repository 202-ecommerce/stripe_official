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

/**
 * Handler for Google or Apple Pay in stripe popin
 * SAME CLASS FOR PS 1.6 AND PS 1.7
 */
class StripePaymentRequestHandler
{

    /**
     * @var PrestaShop logger
     */
    private static $handler = null;

    private $shippingOptions;

    public $cart;

    public function __construct()
    {
        $this->context = Context::getContext();
        StripeLogger::logInfo('PaymentRequest: cart initial #' . (int) $this->context->cookie->id_cart);
        if (empty($this->context->cookie->id_cart)) {
            $this->cart = new Cart();
            $this->cart->id_lang = (int)$this->context->cookie->id_lang;
            $this->cart->id_currency = (int) $this->context->cookie->id_currency;
            $this->cart->id_guest = (int)$this->context->cookie->id_guest;
            $this->cart->id_shop_group = (int)$this->context->shop->id_shop_group;
            $this->cart->id_shop = $this->context->shop->id;
            $this->cart->save();
            $this->context->cart = $this->cart;
        } else {
            $this->cart = new Cart($this->context->cookie->id_cart);
        }
    }

    /**
     * instanciate Stripeid_customerHandler
     */
    public static function getInstance()
    {
        if (self::$handler === null) {
            $handler = new StripePaymentRequestHandler();
            self::$handler = $handler;
        }

        return self::$handler;
    }

    /**
     * changeCartProducts
     */
    public function changeCartProducts()
    {
        $products = $this->cart->getProducts();

        $id_product = (int) Tools::getValue('id_product');
        $id_product_attribute = (int) Tools::getValue('id_product_attribute');
        $quantity = (int) Tools::getValue('quantity');
        if ($quantity == 0) {
            $quantity = 1;
        }
        if ($id_product_attribute == 0) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }

        StripeLogger::logInfo(
            'PaymentRequest: changeCartProducts id_product #' . $id_product .
            ' id_att #' . $id_product_attribute .' quantity: ' . $quantity
        );

        foreach ($products as $product) {
            $this->cart->updateQty(0, $product['id_product'], $product['id_product_attribute']);
        }
        $this->cart->updateQty(
            $quantity,
            $id_product,
            $id_product_attribute,
            false,
            'up',
            0
        );
        StripeLogger::logInfo('PaymentRequest: changeCartProducts #' . print_r($this->cart->getProducts(), true));

        return $this;
    }

    /**
     * changeCartAddress
     */
    public function changeCartAddress()
    {
        if (Tools::getValue('address') == null) {
            return $this;
        }
        $address = Tools::getValue('address');
        StripeLogger::logInfo('PaymentRequest: changeCartAddress ' . print_r($address, true));
        // manage incomplete address according to PrestaShop
        if (empty($address['recipient'])) {
            $firstname = "tmpname";
            $lastname = "tmpname";
        } elseif (Validate::isName($address['recipient']) == false) {
            throw new StripePaymentRequestException('Name is empty');
        } else {
            // only one field for the first or in stripe popin
            $fullname = explode(' ', $address['recipient']);
            $firstname = $fullname[0];
            $lastname = $firstname;
            if (!empty($fullname[1])) {
                $lastname = $fullname[1];
            }
        }

        if (!empty($address['recipient']) && empty($address['addressLine'][0])) {
            throw new StripePaymentRequestException('address line 0 is empty');
        } elseif (empty($address['addressLine'][0])) {
            $address['addressLine'][0] = "tmpname";
        }

        $city = $address['city'];
        $id_country = Country::getByIso($address['country']);
        $line1 = $address['addressLine'][0];
        $line2 = '';
        if (!empty($address['addressLine'][1])) {
            $line2 = $address['addressLine'][1];
        }
        $postal_code = $address['postalCode'];
        $phone = $address['phone'];

        $idAddress = $this->isAddressExists($postal_code, $city, $phone, $id_country);
        if ($idAddress == false) {
            $newAddress = new Address();
            $newAddress->id_customer = $this->context->customer->id;
            $newAddress->id_country = $id_country;
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
                StripeLogger::logInfo('PaymentRequest: create new adress #' . $newAddress->id);
            } catch (Exception $e) {
                throw new StripePaymentRequestException('Create new adress ERROR catched ' . $e->getMessage());
            }

            $idAddress = $newAddress->id;
        }

        $this->cart->id_address_delivery = $idAddress;
        $this->cart->id_address_invoice = $idAddress;

        return $this;
    }

    /**
     * changeCartProducts
     */
    public function loadShippingOptions()
    {
        $id_zone = Address::getZoneById($this->cart->id_address_delivery);
        $this->context->country->id_zone = $id_zone;
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, $id_zone);

        $product = new Product(Tools::getValue('id_product'));
        $productCarriers = $product->getCarriers();
        $productCarriersId = array();
        foreach ($productCarriers as $productCarrier) {
            $productCarriersId[] = $productCarrier['id_carrier'];
        }

        $this->shippingOptions = array();
        $key = 0;
        foreach ($carriers as $carrier) {
            // do not use all carrier if specific carriers set in a product
            if (!empty($productCarriersId) && !in_array($carrier['id_carrier'], $productCarriersId)) {
                StripeLogger::logInfo('PaymentRequest: Carrier #'.$carrier['id_carrier']
                .' skip due to specific carriers set in a product');
                continue;
            }
            $carrierPrice = $this->cart->getPackageShippingCost($carrier['id_carrier']);
            $carrierPrice = $this->isZeroDecimalCurrency() ? $carrierPrice : $carrierPrice * 100;
            $this->shippingOptions[$key]['id'] = $carrier['id_carrier'];
            $this->shippingOptions[$key]['label'] = $carrier['name'];
            $this->shippingOptions[$key]['detail'] = $carrier['delay'];
            $this->shippingOptions[$key]['amount'] = $carrierPrice;
            $key++;
        }
        StripeLogger::logInfo('PaymentRequest: loadShippingOptions' . print_r($this->shippingOptions, true));

        return $this;
    }

    /**
     * changeCartCarrier
     */
    public function changeCartCarrier()
    {
        if (empty($this->shippingOptions)) {
            throw new StripePaymentRequestException('No shipping zone found for this address');
        }
        if (Tools::getValue('carrier') == null) {
            $this->cart->id_carrier = $this->shippingOptions[0]['id'];
            return $this;
        }
        $this->cart->id_carrier = Tools::getValue('carrier');
        StripeLogger::logInfo('PaymentRequest: changeCartCarrier #' . Tools::getValue('carrier'));

        return $this;
    }

    /**
     * changeCartCarrier
     */
    public function changeCustomer()
    {
        // curstormer already created
        if ($this->cart->id_customer != null) {
            return $this;
        }

        // not enough data to create customer
        if (Tools::getValue('payerEmail') == null || Tools::getValue('payerName') == null) {
            return $this;
        }

        $address_delivery = new Address($this->cart->id_address_delivery);

        $customer = new Customer();
        $customer->id_shop_group = (int)$this->context->shop->id_shop_group;
        $customer->id_shop = $this->context->shop->id;
        $customer->id_gender = 0;
        $customer->id_default_group = 2;
        $customer->id_lang = (int)$this->context->cookie->id_lang;
        $customer->id_risk = 0;
        $customer->firstname = $address_delivery->firstname;
        $customer->lastname = $address_delivery->lastname;
        $customer->email = $address_delivery->email;
        $customer->passwd = md5(Tools::passwdGen(8, 'RANDOM'));
        $customer->newsletter = 0;
        $customer->optin = 0;
        $customer->active = 1;
        $customer->is_guest = 1;
        $customer->save();
        StripeLogger::logInfo('PaymentRequest: createGuest customer #' . $customer->id);

        $this->cart->id_customer = $customer->id;

        return $this;
    }

    /**
     * getShippingOptions
     */
    public function getShippingOptions()
    {
        return $this->shippingOptions;
    }

    /**
     * getAmount
     */
    public function getAmount()
    {
        $amount = $this->cart->getOrderTotal(true, Cart::BOTH, null, $this->cart->id_carrier);
        $amount = $this->isZeroDecimalCurrency() ? $amount : $amount * 100;
        StripeLogger::logInfo('id_customer: getOrderTotal ' . $amount);

        return $amount;
    }

    /**
     * close handler
     */
    public function close()
    {
        $this->cart->save();
        $this->context->cookie->__set('id_cart', $this->cart->id);
        $this->context->cookie->write();

        return $this;
    }

    /**
     * getLabel
     */
    public function getLabel()
    {
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            return $product['cart_quantity'] . ' x ' . $product['name'];
        }

        return 'Amount';
    }

    /**
     * check when a user change of address or change of carrier if the carrier
     * @param string $postcode zip
     * @param string $city     city
     * @param string $phone    phone number
     * @param string $country  id of the country
     */
    private function isAddressExists($postcode, $city, $phone, $country)
    {
        $sql = "SELECT
                    id_address
                FROM
                    "._DB_PREFIX_."address
                WHERE
                    postcode='".pSQL($postcode)."'
                    AND city='".pSQL($city)."'
                    AND phone='".pSQL($phone)."'
                    AND id_country=" . (int) $country . "
                    AND id_customer=".(int) $this->context->customer->id;
        StripeLogger::logInfo('PaymentRequest: addressExists sql request' . $sql);

        $result = DB::getInstance()->getValue($sql);
        StripeLogger::logInfo('PaymentRequest: addressExists adress #' . $result);

        if ($result != '') {
            return $result;
        } else {
            return false;
        }
    }

    public function isZeroDecimalCurrency()
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
        return in_array((int) $this->context->cookie->id_currency, $zeroDecimalCurrencies);
    }
}
