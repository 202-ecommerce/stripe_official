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
 * @version   develop
 */

namespace Stripe_officialClasslib\Database;

use Stripe_officialClasslib\Database\Action\ActionFactory;
use Stripe_officialClasslib\Database\Action\ActionType;
use Stripe_officialClasslib\Database\Definition\ObjectModel\ObjectModelDefinition;
use ObjectModel;

class ObjectModelExtension
{
    /**
     * @var ObjectModelDefinition
     */
    private $objectModelDefinition;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @param string $objectModel
     *
     * @throws \PrestaShopException
     */
    public function __construct(string $objectModel)
    {
        $this->objectModelDefinition = (new ObjectModelDefinition($objectModel))->build($objectModel::$definition);
        $this->actionFactory = new ActionFactory();
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function install()
    {
        return $this->actionFactory
            ->getAction(ActionType::INSTALL)
            ->performAction($this->objectModelDefinition);
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function uninstall()
    {
        return $this->actionFactory
            ->getAction(ActionType::UNINSTALL)
            ->performAction($this->objectModelDefinition);
    }
}
