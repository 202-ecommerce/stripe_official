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
use Stripe_officialClasslib\Database\Index\IndexHandler;
use Db;

class UpdateTableAction extends AbstractTableAction
{
    /**
     * @param TableDefinition $tableDefinition
     *
     * @return bool
     */
    public function handle(TableDefinition $tableDefinition)
    {
        $result = true;

        $result &= $this->modifyColumns($tableDefinition);
        $result &= $this->handleIndexes($tableDefinition);

        return $result;
    }

    protected function modifyColumns(TableDefinition $tableDefinition)
    {
        $tableColumns = $this->getTableColumns($tableDefinition);

        $columns = array_map(function (FieldDefinition $fieldDefinition) {
            return $fieldDefinition->getColumn();
        }, $tableDefinition->getFields());

        foreach ($tableColumns as &$col) {
            $col['modelDef'] = '`' . $col['Field'] . '` ' . strtoupper($col['Type']) . ' ';
            if ('NO' === $col['Null']) {
                $col['modelDef'] .= 'NOT NULL ';
            }
            if (false === empty($col['Extra'])) {
                $col['modelDef'] .= strtoupper($col['Extra']);
            }
        }

        $alterToSkip = [];
        $alterToExecute = [];
        $alters = [];

        foreach ($columns as $key => $column) {
            foreach ($tableColumns as $tableColumn) {
                if (trim($column) === trim($tableColumn['modelDef'])) {
                    $alterToSkip[$key] = true;
                } elseif (false !== strpos($column, '`' . $tableColumn['Field'] . '`')) {
                    $alterToExecute[$key] = 'MODIFY';
                    $alters[$key] = "ALTER TABLE `{$tableDefinition->getName()}` MODIFY $column;";
                }
            }
            if (empty($alterToExecute[$key]) && empty($alterToSkip[$key])) {
                $alterToExecute[$key]['action'] = 'ADD ' . $column;
                $alters[$key] = "ALTER TABLE `{$tableDefinition->getName()}` ADD $column;";
            }
        }

        $result = true;
        foreach ($alters as $alter) {
            $result &= Db::getInstance()->execute($alter, false);
        }

        $tableDefinitionColumns = array_map(function (FieldDefinition $fieldDefinition) {
            return $fieldDefinition->getName();
        }, $tableDefinition->getFields());

        $currentTableColumns = array_column($tableColumns, 'Field');
        $columnsToDelete = array_diff($currentTableColumns, $tableDefinitionColumns);

        foreach ($columnsToDelete as $columnToDelete) {
            Db::getInstance()->execute("
                    ALTER TABLE  `{$tableDefinition->getName()}`
                    DROP COLUMN {$columnToDelete};"
            );
        }

        return $result;
    }

    protected function handleIndexes(TableDefinition $tableDefinition)
    {
        $indexHandler = new IndexHandler($tableDefinition);

        return $indexHandler->handle();
    }

    protected function getTableColumns(TableDefinition $tableDefinition)
    {
        return Db::getInstance()->executeS("SHOW COLUMNS FROM `{$tableDefinition->getName()}`");
    }
}
