<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
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

namespace Stripe_officialClasslib\Install;

use \Configuration;
use \Db;
use \DbQuery;
use \Language;
use \OrderState;
use \Tools;
use Stripe_officialClasslib\Install\AbstractInstaller;
use Stripe_officialClasslib\Install\ExtensionInstaller;

class ModuleInstaller extends AbstractInstaller
{
    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function install()
    {
        $result = parent::install();
        $result &= $this->registerOrderStates();
        $result &= $this->installExtensions();

        return $result;
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function uninstall()
    {
        $result = parent::uninstall();
        $result &= $this->uninstallConfiguration();
        $result &= $this->unregisterOrderStates();
        $result &= $this->uninstallExtensions();

        return $result;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function checkPhpVersion()
    {
        if (empty($this->module->php_version_required)) {
            return true;
        }

        $phpVersion = Tools::checkPhpVersion();

        if (Tools::version_compare($phpVersion, $this->module->php_version_required, '<')) {
            throw new \Exception(sprintf(
                '[%s] This module requires at least PHP %s or newer versions.',
                $this->module->name,
                $this->module->php_version_required
            ));
        }

        return true;
    }

    /**
     * Uninstall Configuration (with or without language management)
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function uninstallConfiguration()
    {
        $query = new DbQuery();
        $query->select('name');
        $query->from('configuration');
        $query->where('name LIKE \'' . pSQL(Tools::strtoupper($this->module->name)) . '_%\'');

        $results = Db::getInstance()->executeS($query);

        if (empty($results)) {
            return true;
        }

        $configurationKeys = array_column($results, 'name');

        $result = true;
        foreach ($configurationKeys as $configurationKey) {
            $result &= Configuration::deleteByName($configurationKey);
        }

        return $result;
    }

    /**
     * Register Order State : create new order state for this module
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function registerOrderStates()
    {
        if (empty($this->module->orderStates)) {
            return true;
        }

        $result = true;
        foreach ($this->module->orderStates as $configurationName => $orderStateParams) {
            $orderState = new OrderState();
            foreach ($orderStateParams as $key => $value) {
                if ($key !== 'name' && property_exists($orderState, $key)) {
                    $orderState->$key = $value;
                }
                if ($key === 'name') {
                    foreach (Language::getLanguages(false) as $language) {
                        if (empty($value[$language['iso_code']])) {
                            $orderState->name[$language['id_lang']] = $value['en'];
                        } else {
                            $orderState->name[$language['id_lang']] = $value[$language['iso_code']];
                        }
                    }
                }
            }
            $orderState->module_name = $this->module->name;
            $result &= (bool)$orderState->save();
            Configuration::updateValue($configurationName, $orderState->id);
        }

        return $result;
    }

    /**
     * Unregister Order State : mark them as deleted
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function unregisterOrderStates()
    {
        if (empty($this->module->orderStates)) {
            return true;
        }

        $query = new DbQuery();
        $query->select('id_order_state');
        $query->from('order_state');
        $query->where('module_name = \'' . pSQL($this->module->name) . '\'');

        $orderStateData = Db::getInstance()->executeS($query);

        if (empty($orderStateData)) {
            return true;
        }

        $result = true;
        foreach ($orderStateData as $data) {
            $query = new DbQuery();
            $query->select('1');
            $query->from('orders');
            $query->where('current_state = ' . $data['id_order_state']);
            $isUsed = (bool)Db::getInstance()->getValue($query);
            $orderState = new OrderState($data['id_order_state']);
            if ($isUsed) {
                $orderState->deleted = true;
                $result &= (bool)$orderState->save();
            } else {
                $result &= (bool)$orderState->delete();
            }
        }

        return $result;
    }

    //region Install/Uninstall Extensions

    public function installExtensions()
    {
        if (!isset($this->module->extensions) || empty($this->module->extensions)) {
            return true;
        }

        return array_product(array_map([$this, 'installExtension'], $this->module->extensions));
    }

    /**
     * @param string $extensionName
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function installExtension($extensionName)
    {
        $extension = new $extensionName($this->module);
        $extensionInstaller = new ExtensionInstaller($this->module, $extension);

        return $extensionInstaller->install();
    }

    public function uninstallExtensions()
    {
        if (!isset($this->module->extensions) || empty($this->module->extensions)) {
            return true;
        }

        return array_product(array_map([$this, 'uninstallExtension'], $this->module->extensions));
    }

    /**
     * @param string $extensionName
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function uninstallExtension($extensionName)
    {
        $extension = new $extensionName($this->module);
        $extensionInstaller = new ExtensionInstaller($this->module, $extension);

        return $extensionInstaller->uninstall();
    }

    //endregion

    //region Getters

    public function getHooks()
    {
        return $this->module->hooks;
    }

    public function getAdminControllers()
    {
        return $this->module->moduleAdminControllers;
    }

    public function getObjectModels()
    {
        return $this->module->objectModels;
    }

    //endregion
}
