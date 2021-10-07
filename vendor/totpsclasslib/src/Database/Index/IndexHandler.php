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

namespace Stripe_officialClasslib\Database\Index;

use Stripe_officialClasslib\Database\DbObjectHandler;
use Stripe_officialClasslib\Database\Definition\Table\TableDefinition;
use Db;
use PrestaShopLogger;

class IndexHandler implements DbObjectHandler
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

    public function handle()
    {
        $keys = $this->getKeys();

        $keyNames = array_column($keys, 'Key_name');
        $keyNames = array_unique($keyNames);

        foreach ($keyNames as $key) {
            $this->dropKey($key);
        }

        foreach ($this->tableDefinition->getIndexes() as $index) {
            $indexSql = $index->getIndex();
            if (empty($indexSql)) {
                continue;
            }

            try {
                Db::getInstance()->execute($indexSql);
            } catch (\Exception $e) {
                PrestaShopLogger::addLog(
                    'Add index : ' . $e->getMessage(),
                    3,
                    null,
                    IndexHandler::class
                );
            }
        }

        return true;
    }

    protected function getKeys()
    {
        return Db::getInstance()->executeS("
                SHOW KEYS FROM `{$this->tableDefinition->getName()}` 
                WHERE Key_name <> 'PRIMARY'"
        );
    }

    protected function dropKey($key)
    {
        try {
            Db::getInstance()->execute("
                ALTER TABLE `{$this->tableDefinition->getName()}` DROP KEY `$key`;"
            );
        } catch (\Exception $e) {
            PrestaShopLogger::addLog(
                'Drop index : ' . $e->getMessage(),
                3,
                null,
                IndexHandler::class
            );
        }
    }

    /**
     * @return TableDefinition
     */
    public function getTableDefinition(): TableDefinition
    {
        return $this->tableDefinition;
    }
}
