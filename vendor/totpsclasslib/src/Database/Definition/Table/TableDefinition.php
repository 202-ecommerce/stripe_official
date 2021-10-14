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

namespace Stripe_officialClasslib\Database\Definition\Table;

use Stripe_officialClasslib\Database\Definition\Field\FieldDefinition;
use Stripe_officialClasslib\Database\ForeignKey\ForeignKey;
use Stripe_officialClasslib\Database\Index\Index;

class TableDefinition
{
    //region Fields

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var FieldDefinition[]
     */
    private $fields;

    /**
     * @var string
     */
    private $engine;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var string
     */
    private $collation;

    /**
     * @var ForeignKey[]
     */
    private $foreignKeys;

    /**
     * @var array
     */
    private $primaryKey;

    /**
     * @var Index[]
     */
    private $indexes = [];

    //endregion

    //region Getters/Setters

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TableDefinition
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return FieldDefinition[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param FieldDefinition[] $fields
     *
     * @return TableDefinition
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

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
     * @return TableDefinition
     */
    public function setEngine(string $engine)
    {
        $this->engine = $engine;

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
     * @return TableDefinition
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
     * @return TableDefinition
     */
    public function setCollation(string $collation)
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * @return ForeignKey[]
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * @param ForeignKey[] $foreignKeys
     *
     * @return TableDefinition
     */
    public function setForeignKeys(array $foreignKeys)
    {
        $this->foreignKeys = $foreignKeys;

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
     * @return TableDefinition
     */
    public function setIndexes(array $indexes)
    {
        $this->indexes = $indexes;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return TableDefinition
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param array $primaryKey
     *
     * @return TableDefinition
     */
    public function setPrimaryKey(array $primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    //endregion
}
