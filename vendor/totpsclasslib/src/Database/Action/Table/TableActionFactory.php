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

namespace Stripe_officialClasslib\Database\Action\Table;

use Stripe_officialClasslib\Database\Action\Table\AbstractTableAction;
use Stripe_officialClasslib\Database\Action\Table\CreateTableAction;
use Stripe_officialClasslib\Database\Action\Table\DeleteTableAction;
use Stripe_officialClasslib\Database\Action\Table\TableActionType;
use Stripe_officialClasslib\Database\Action\Table\UpdateTableAction;
use PrestaShopException;

class TableActionFactory
{
    /**
     * @param $tableAction
     *
     * @return AbstractTableAction
     *
     * @throws PrestaShopException
     */
    public function getTableAction($tableAction)
    {
        switch ($tableAction) {
            case TableActionType::CREATE:
                return new CreateTableAction();
            case TableActionType::UPDATE:
                return new UpdateTableAction();
            case TableActionType::DELETE:
                return new DeleteTableAction();
            default:
                throw new PrestaShopException("Unknown table action $tableAction");
        }
    }
}
