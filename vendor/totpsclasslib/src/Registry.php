<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommence
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommence is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommence
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommence est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 *
 * @version   release/2.3.1
 */

namespace Stripe_officialClasslib;

/**
 * Design pattern Registry
 */
class Registry
{
    /**
     * @var Registry Instance of this class
     */
    private static $_registry = null;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Get instance of this class
     *
     * @return Registry
     */
    public static function getInstance()
    {
        if (self::$_registry === null) {
            self::$_registry = new Registry();
        }

        return self::$_registry;
    }

    /**
     * Get a variable in the Registry
     *
     * @param string $index
     *
     * @return mixed
     */
    public static function get($index)
    {
        $instance = self::getInstance();
        if (!$instance->offsetExists($index)) {
            return false;
        }

        return $instance->values[$index];
    }

    /**
     * Set a variable in the Registry
     *
     * @param string $index
     * @param string|array $value
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->values[$index] = $value;
    }

    /**
     * Check if var exist in the registry
     *
     * @param string $index
     *
     * @return bool
     */
    public static function isRegistered($index)
    {
        if (self::$_registry === null) {
            return false;
        }

        return self::$_registry->offsetExists($index);
    }

    /**
     * Check if offsetExists
     *
     * @param string $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        if (false === isset($this->values)) {
            return false;
        }

        return array_key_exists($index, $this->values);
    }

    /**
     * Increment a counter in the Registry
     *
     * @param string $index
     * @param int $value
     */
    public static function increment($index, $value = 1)
    {
        $instance = self::getInstance();
        if (self::isRegistered($index)) {
            $instance->values[$index] += $value;
        } else {
            $instance->values[$index] = $value;
        }
    }

    /**
     * Decrement a counter in the Registry
     *
     * @param string $index
     * @param int $value
     */
    public static function decrement($index, $value = 1)
    {
        $instance = self::getInstance();
        if (self::isRegistered($index)) {
            $instance->values[$index] -= $value;
        } else {
            $instance->values[$index] = $value * (-1);
        }
    }
}
