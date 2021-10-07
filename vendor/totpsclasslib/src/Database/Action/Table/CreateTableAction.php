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

use Stripe_officialClasslib\Database\Definition\Field\FieldDefinition;
use Stripe_officialClasslib\Database\Definition\Table\TableDefinition;
use Db;

class CreateTableAction extends AbstractTableAction
{
    /**
     * @param TableDefinition $tableDefinition
     *
     * @return bool
     */
    public function handle(TableDefinition $tableDefinition)
    {
        $result = true;

        $result &= $this->installTable($tableDefinition);
        $result &= $this->createPrimaryKey($tableDefinition);

        return $result;
    }

    protected function installTable(TableDefinition $tableDefinition)
    {
        $columns = array_map(function (FieldDefinition $fieldDefinition) {
            return $fieldDefinition->getColumn();
        }, $tableDefinition->getFields());

        return Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `{$tableDefinition->getName()}` (" .
            implode(', ', array_merge($columns, $this->getPrimaryKeySql($tableDefinition))) .
            ") ENGINE={$tableDefinition->getEngine()} CHARSET={$tableDefinition->getCharset()} COLLATE={$tableDefinition->getCollation()};");
    }

    protected function createPrimaryKey(TableDefinition $tableDefinition)
    {
        if (count($tableDefinition->getPrimaryKey()) == 1) {
            return true;
        }

        if ($this->hasPrimaryKey($tableDefinition)) {
            $this->dropPrimaryKey($tableDefinition);
        }

        return $this->addPrimaryKey($tableDefinition);
    }

    protected function hasPrimaryKey(TableDefinition $tableDefinition)
    {
        $hasKey = Db::getInstance()->getValue(
            "SELECT EXISTS(
                   SELECT *
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME='" . $tableDefinition->getName() . "'
                    AND TABLE_SCHEMA = '" . _DB_NAME_ . "'
                    AND COLUMN_KEY = 'PRI'
           );", false);

        return !empty($hasKey);
    }

    protected function dropPrimaryKey(TableDefinition $tableDefinition)
    {
        return Db::getInstance()->execute(
            "ALTER TABLE {$tableDefinition->getName()}
                  DROP PRIMARY KEY; 
        ", false);
    }

    protected function addPrimaryKey(TableDefinition $tableDefinition)
    {
        $pkName = 'PK_' . strtoupper($tableDefinition->getName());
        $pkFields = implode(', ', $tableDefinition->getPrimaryKey());

        return Db::getInstance()->execute(
            "ALTER TABLE {$tableDefinition->getName()}
                 ADD CONSTRAINT $pkName PRIMARY KEY ($pkFields)", false);
    }

    protected function getPrimaryKeySql(TableDefinition $tableDefinition)
    {
        if (count($tableDefinition->getPrimaryKey()) > 1) {
            return [];
        }

        return ["PRIMARY KEY ({$tableDefinition->getPrimaryKey()[0]})"];
    }
}
