<?php

namespace Stripe_officialClasslib\Install;


use \Module;
use \Db;
use \DbQuery;
use \PrestaShopDatabaseException;
use \PrestaShopException;
use \Tab;
use \Language;
use Stripe_officialClasslib\Db\ObjectModelExtension;
use Stripe_officialClasslib\Extensions\AbstractModuleExtension;


class ExtensionInstaller extends AbstractInstaller
{
    //region Fields

    /**
     * @var AbstractModuleExtension
     */
    protected $extension;

    //endregion

    public function __construct($module, $extension = null)
    {
        parent::__construct($module);
        $this->extension = $extension;
    }


    //region Get-Set

    /**
     * @return array
     * @throws PrestaShopException
     */
    public function getHooks()
    {
        if ($this->extension == null) {
            throw new PrestaShopException('Extension is null, can\'t get extension\'s hooks');
        }

        return $this->extension->hooks;
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public function getAdminControllers()
    {
        if ($this->extension == null) {
            throw new PrestaShopException('Extension is null, can\'t get extension\'s admin controllers');
        }

        return $this->extension->extensionAdminControllers;
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public function getObjectModels()
    {
        if ($this->extension == null) {
            throw new PrestaShopException('Extension is null, can\'t get extension\'s object models');
        }

        return $this->extension->objectModels;
    }

    /**
     * @return AbstractModuleExtension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param AbstractModuleExtension $extension
     * @return ExtensionInstaller
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    //endregion
}