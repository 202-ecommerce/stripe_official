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

namespace Stripe_officialClasslib\Database\ForeignKey;

use Stripe_officialClasslib\Database\DbObjectHandler;
use Stripe_officialClasslib\Database\Definition\Table\TableDefinition;
use Db;
use PrestaShopLogger;

class ForeignKeyHandler implements DbObjectHandler
{
    /**
     * @var TableDefinition
     */
    protected $tableDefinition;

    /**
     * @param TableDefinition $tableDefinition
     */
    public function __construct(TableDefinition $tableDefinition)
    {
        $this->tableDefinition = $tableDefinition;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (empty($this->tableDefinition->getForeignKeys())) {
            return true;
        }

        $keys = $this->getKeys();

        $keyNames = array_column($keys, 'CONSTRAINT_NAME');
        $keyNames = array_unique($keyNames);

        foreach ($keyNames as $key) {
            $this->dropKey($key);
        }

        foreach ($this->tableDefinition->getForeignKeys() as $foreignKey) {
            $foreignKeySql = $foreignKey->getForeignKey();
            if (empty($foreignKeySql)) {
                continue;
            }

            try {
                Db::getInstance()->execute($foreignKeySql);
            } catch (\Exception $e) {
                PrestaShopLogger::addLog(
                    'Add foreign key : ' . $e->getMessage(),
                    3,
                    null,
                    self::class
                );
            }
        }

        return true;
    }

    protected function getKeys()
    {
        return Db::getInstance()->executeS("
               SELECT CONSTRAINT_NAME
               FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
               WHERE REFERENCED_TABLE_SCHEMA = '" . _DB_NAME_ . "'
               AND TABLE_NAME = '" . $this->tableDefinition->getName() . "';
        ");
    }

    protected function dropKey($key)
    {
        try {
            Db::getInstance()->execute("
                ALTER TABLE `{$this->tableDefinition->getName()}` DROP FOREIGN KEY `$key`;"
            );
        } catch (\Exception $e) {
            PrestaShopLogger::addLog(
                "Drop foreign key : `$key` " . $e->getMessage(),
                3,
                null,
                self::class
            );
        }
    }
}
