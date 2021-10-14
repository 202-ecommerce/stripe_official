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

namespace Stripe_officialClasslib\Database\Definition\ObjectModel;

use Stripe_officialClasslib\Database\Definition\Field\FieldDefinition;
use Stripe_officialClasslib\Database\ForeignKey\ForeignKey;
use Stripe_officialClasslib\Database\Index\Index;

class ObjectModelDefinition
{
    //region Fields

    /**
     * @var string
     */
    private $objectName;

    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $primary;

    /**
     * @var bool
     */
    private $multilang = false;

    /**
     * @var bool
     */
    private $multilangShop = false;

    /**
     * @var bool
     */
    private $multishop = false;

    /**
     * @var FieldDefinition[]
     */
    private $fields = [];

    /**
     * @var array
     */
    private $associations = [];

    /**
     * @var Index[]
     */
    private $indexes = [];

    /**
     * @var string
     */
    private $charset = 'utf8';

    /**
     * @var string
     */
    private $collation = 'utf8_general_ci';

    /**
     * @var string
     */
    private $engine = _MYSQL_ENGINE_;

    /**
     * @var string
     */
    private $dbPrefix = _DB_PREFIX_;

    //endregion

    //region Getters/Setters

    /**
     * @param string $objectName
     */
    public function __construct(string $objectName)
    {
        $this->objectName = $objectName;
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @param string $objectName
     *
     * @return ObjectModelDefinition
     */
    public function setObjectName($objectName)
    {
        $this->objectName = $objectName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return ObjectModelDefinition
     */
    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return array
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @param string $primary
     *
     * @return ObjectModelDefinition
     */
    public function setPrimary(string $primary)
    {
        $this->primary = array_map(function ($primaryKey) {
            return trim($primaryKey);
        }, explode(',', $primary));

        return $this;
    }

    /**
     * @return bool
     */
    public function isMultilang()
    {
        return $this->multilang;
    }

    /**
     * @param bool $multilang
     *
     * @return ObjectModelDefinition
     */
    public function setMultilang(bool $multilang)
    {
        $this->multilang = $multilang;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMultilangShop()
    {
        return $this->multilangShop;
    }

    /**
     * @param bool $multilangShop
     *
     * @return ObjectModelDefinition
     */
    public function setMultilangShop(bool $multilangShop)
    {
        $this->multilangShop = $multilangShop;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMultishop()
    {
        return $this->multishop;
    }

    /**
     * @param bool $multishop
     *
     * @return ObjectModelDefinition
     */
    public function setMultishop(bool $multishop)
    {
        $this->multishop = $multishop;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return ObjectModelDefinition
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param array $associations
     *
     * @return ObjectModelDefinition
     */
    public function setAssociations(array $associations)
    {
        $this->associations = $associations;

        return $this;
    }

    /**
     * @return Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param Index[] $indexes
     *
     * @return ObjectModelDefinition
     */
    public function setIndexes(array $indexes)
    {
        $this->indexes = $indexes;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return ObjectModelDefinition
     */
    public function setCharset(string $charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * @param string $collation
     *
     * @return ObjectModelDefinition
     */
    public function setCollation(string $collation)
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     *
     * @return ObjectModelDefinition
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbPrefix()
    {
        return $this->dbPrefix;
    }

    /**
     * @param string $dbPrefix
     *
     * @return ObjectModelDefinition
     */
    public function setDbPrefix($dbPrefix)
    {
        $this->dbPrefix = $dbPrefix;

        return $this;
    }

    //endregion

    /**
     * @param array $definition
     *
     * @return $this
     *
     * @throws \PrestaShopException
     */
    public function build($definition)
    {
        $this->setTable($definition['table'])
            ->setPrimary($definition['primary']);

        if (isset($definition['charset'])) {
            $this->setCharset($definition['charset']);
        }

        if (isset($definition['collation'])) {
            $this->setCollation($definition['collation']);
        }

        if (isset($definition['engine'])) {
            $this->setEngine($definition['engine']);
        }

        if (isset($definition['multilang'])) {
            $this->setMultilang($definition['multilang']);
        }

        if (isset($definition['multishop'])) {
            $this->setMultishop($definition['multishop']);
        }

        if (isset($definition['multilang_shop'])) {
            $this->setMultilangShop($definition['multilang_shop']);
        }

        if (isset($definition['indexes'])) {
            $indexes = [];
            foreach ($definition['indexes'] as $index) {
                $indexes[] = Index::build($index, $this->dbPrefix . $definition['table']);
            }
            $this->setIndexes($indexes);
        }

        if (isset($definition['fields'])) {
            $fields = [];
            foreach ($definition['fields'] as $field => $description) {
                $fieldDefinition = new FieldDefinition($field, $description);
                $fields[] = $fieldDefinition;
            }
            $this->setFields($fields);
        }

        if (isset($definition['associations'])) {
            $associations = [];
            foreach ($definition['associations'] as $name => $association) {
                $foreignKey = (new ForeignKey())->build($this->getDbPrefix() . $definition['table'], $association);
                $associations[] = $foreignKey;
            }
            $this->setAssociations($associations);
        }

        return $this;
    }
}
