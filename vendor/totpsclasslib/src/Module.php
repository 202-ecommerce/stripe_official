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
 * @version   develop
 */

namespace Stripe_officialClasslib;

use Stripe_officialClasslib\Hook\AbstractHookDispatcher;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use \ReflectionClass;
use \Tools;
use Stripe_officialClasslib\Install\ModuleInstaller;
use Stripe_officialClasslib\Extensions\AbstractModuleExtension;

class Module extends \Module
{
    //region Fields

    /**
     * List of objectModel used in this Module
     * @var array $objectModels
     */
    public $objectModels = array();

    /**
     * List of hooks used in this Module
     * @var array $hooks
     */
    public $hooks = array();

    public $extensions = array();

    /**
     * @var AbstractHookDispatcher
     */
    protected $hookDispatcher = null;

    /**
     * List of AdminControllers used in this Module
     * @var array $moduleAdminControllers
     */
    public $moduleAdminControllers = array();

    //endregion

    /**
     * Module constructor.
     */
    public function __construct()
    {
        parent::__construct();
        foreach ($this->extensions as $extensionName) {
            /** @var AbstractModuleExtension $extension */
            $extension = new $extensionName();
            $extension->setModule($this);
            $extension->initExtension();
        }
    }

    /**
     * Install Module
     *
     * @return bool
     * @throws \PrestaShopException
     */
    public function install()
    {
        $installer = new ModuleInstaller($this);

        $isPhpVersionCompliant = false;
        try {
            $isPhpVersionCompliant = $installer->checkPhpVersion();
        } catch (\Exception $e) {
            $this->_errors[] = Tools::displayError($e->getMessage());
        }

        return $isPhpVersionCompliant && parent::install() && $installer->install();
    }

    /**
     * Uninstall Module
     *
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function uninstall()
    {
        $installer = new ModuleInstaller($this);

        return parent::uninstall() && $installer->uninstall();
    }

    /**
     * @TODO Reset Module only if merchant choose to keep data on modal
     *
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function reset()
    {
        $installer = new ModuleInstaller($this);

        return $installer->reset($this);
    }

    /**
     * Handle module extension hook call
     *
     * @param $hookName
     * @param $params
     * @return array|bool|string
     */
    public function handleExtensionsHook($hookName, $params)
    {
        $result = false;

        // execute module hooks
        if ($this->getHookDispatcher() != null) {
            $moduleHookResult = $this->getHookDispatcher()->dispatch($hookName, $params);
            if ($moduleHookResult != null) {
                $result = $moduleHookResult;
            }
        }

        //execute extension's hooks
        if (!isset($this->extensions) || empty($this->extensions)) {
            if (!$result) {
                return false;
            }
        }

        foreach ($this->extensions as $extension) {
            /** @var AbstractModuleExtension $extension */
            $extension = new $extension($this);
            $hookResult = null;
            if (is_callable(array($extension, $hookName))) {
                $hookResult = $extension->{$hookName}($params);
                //TODO
            } else if (is_callable(array($extension, 'getHookDispatcher')) && $extension->getHookDispatcher() != null) {
                $hookResult = $extension->getHookDispatcher()->dispatch($hookName, $params);
            }
            if ($hookResult != null) {
                if ($result === false) {
                    $result = $hookResult;
                } elseif (is_array($hookResult) && $result !== false) {
                    $result = array_merge($result, $hookResult);
                } else {
                    $result .= $hookResult;
                }
            }
        }

        return $result;
    }

    /**
     * Handle module widget call
     *
     * @param $action
     * @param $method
     * @param $hookName
     * @param $configuration
     * @return bool
     * @throws \ReflectionException
     * @deprecated use render widget function
     */
    public function handleWidget($action, $method, $hookName, $configuration)
    {
        if (!isset($this->extensions) || empty($this->extensions)) {
            return false;
        }

        foreach ($this->extensions as $extension) {
            /** @var AbstractModuleExtension $extension */
            $extension = new $extension();
            if (!($extension instanceof WidgetInterface)) {
                continue;
            }
            $extensionClass = (new ReflectionClass($extension))->getShortName();
            if ($extensionClass != $action) {
                continue;
            }
            $extension->setModule($this);
            if (is_callable(array($extension, $method))) {
                return $extension->{$method}($hookName, $configuration);
            }
        }

        return false;

    }

    /**
     * @param $hookName
     * @param array $configuration
     * @return bool
     * @throws \ReflectionException
     */
    public function renderWidget($hookName, array $configuration)
    {
        // render module widgets
        if ($this->getHookDispatcher() != null) {
            $moduleWidgetResult = $this->getHookDispatcher()->dispatch($hookName, $configuration);
            if ($moduleWidgetResult != null) {
                return $moduleWidgetResult;
            }
        }

        // render extensions widget if module widget isn't found
        if (!isset($this->extensions) || empty($this->extensions)) {
            return false;
        }

        foreach ($this->extensions as $extension) {
            /** @var AbstractModuleExtension $extension */
            $extension = new $extension($this);

            if (is_callable(array($extension, 'getHookDispatcher')) && $extension->getHookDispatcher() != null) {
                return $extension->getHookDispatcher()->dispatch($hookName, $configuration);
            }
        }

        //if we want to use an old approach
        return $this->handleWidget($configuration['action'], __FUNCTION__, $hookName, $configuration);
    }

    /**
     * @param $hookName
     * @param array $configuration
     * @return array|bool
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        return array();
    }

    /**
     * Get the current module hook/widget dispatcher
     * @return null
     */
    public function getHookDispatcher()
    {
        return $this->hookDispatcher;
    }
}
