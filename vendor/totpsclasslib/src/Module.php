<?php
/**
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

namespace Stripe_officialClasslib;

use Stripe_officialClasslib\Extensions\AbstractModuleExtension;
use Stripe_officialClasslib\Hook\AbstractHookDispatcher;
use Stripe_officialClasslib\Install\ModuleInstaller;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use ReflectionClass;
use Tools;

class Module extends \Module
{
    //region Fields

    /**
     * List of objectModel used in this Module
     *
     * @var array
     */
    public $objectModels = [];

    /**
     * List of hooks used in this Module
     *
     * @var array
     */
    public $hooks = [];

    /**
     * @var array
     */
    public $extensions = [];

    /**
     * @var AbstractHookDispatcher
     */
    protected $hookDispatcher = null;

    /**
     * List of AdminControllers used in this Module
     *
     * @var array
     */
    public $moduleAdminControllers = [];

    /**
     * @var string
     */
    public $secure_key;

    /**
     * @var array
     */
    public $cronTasks = [];

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
            $this->hooks = array_merge($this->hooks, $extension->hooks);
        }
    }

    /**
     * Install Module
     *
     * @return bool
     *
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
     *
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
     *
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
     * @param string $hookName
     * @param array $params
     *
     * @return array|bool|string
     */
    public function handleExtensionsHook($hookName, $params)
    {
        $result = false;
        $hookDispatcher = $this->getHookDispatcher();

        // execute module hooks
        if ($hookDispatcher != null) {
            $moduleHookResult = $hookDispatcher->dispatch($hookName, $params);
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
            if (is_callable([$extension, $hookName])) {
                $hookResult = $extension->{$hookName}($params);
            //TODO
            } elseif (is_callable([$extension, 'getHookDispatcher']) && $extension->getHookDispatcher() != null) {
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
     * @param string $action
     * @param string $method
     * @param string $hookName
     * @param array $configuration
     *
     * @return bool
     *
     * @throws \ReflectionException
     *
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
            if (is_callable([$extension, $method])) {
                return $extension->{$method}($hookName, $configuration);
            }
        }

        return false;
    }

    /**
     * @param string $hookName
     * @param array $configuration
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public function renderWidget($hookName, array $configuration)
    {
        $hookDispatcher = $this->getHookDispatcher();
        // render module widgets
        if ($hookDispatcher != null) {
            $moduleWidgetResult = $hookDispatcher->dispatch($hookName, $configuration);
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

            if (is_callable([$extension, 'getHookDispatcher']) && $extension->getHookDispatcher() != null) {
                $extensionWidgetResult = $extension->getHookDispatcher()->dispatch($hookName, $configuration);
                if (is_null($extensionWidgetResult)) {
                    continue;
                }

                return $extensionWidgetResult;
            }
        }

        //if we want to use an old approach
        return $this->handleWidget($configuration['action'], __FUNCTION__, $hookName, $configuration);
    }

    /**
     * @param string $hookName
     * @param array $configuration
     *
     * @return array|bool
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        return [];
    }

    /**
     * Get the current module hook/widget dispatcher
     *
     * @return AbstractHookDispatcher|null
     */
    public function getHookDispatcher()
    {
        return $this->hookDispatcher;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->_path;
    }
}
