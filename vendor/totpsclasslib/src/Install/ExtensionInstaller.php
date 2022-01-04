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

    public function install()
    {
        return parent::install() && $this->extension->install();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->extension->uninstall();
    }

    //region Get-Set

    /**
     * @return array
     *
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
     *
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
     *
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
     *
     * @return ExtensionInstaller
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    //endregion
}
