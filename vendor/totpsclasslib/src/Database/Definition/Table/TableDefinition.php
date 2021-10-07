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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TableDefinition
     */
    public function setName(string $name): TableDefinition
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return FieldDefinition[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param FieldDefinition[] $fields
     *
     * @return TableDefinition
     */
    public function setFields(array $fields): TableDefinition
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     *
     * @return TableDefinition
     */
    public function setEngine(string $engine): TableDefinition
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return TableDefinition
     */
    public function setCharset(string $charset): TableDefinition
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string
     */
    public function getCollation(): string
    {
        return $this->collation;
    }

    /**
     * @param string $collation
     *
     * @return TableDefinition
     */
    public function setCollation(string $collation): TableDefinition
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * @return ForeignKey[]
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * @param ForeignKey[] $foreignKeys
     *
     * @return TableDefinition
     */
    public function setForeignKeys(array $foreignKeys): TableDefinition
    {
        $this->foreignKeys = $foreignKeys;

        return $this;
    }

    /**
     * @return Index[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param Index[] $indexes
     *
     * @return TableDefinition
     */
    public function setIndexes(array $indexes): TableDefinition
    {
        $this->indexes = $indexes;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return TableDefinition
     */
    public function setAlias(string $alias): TableDefinition
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    /**
     * @param array $primaryKey
     *
     * @return TableDefinition
     */
    public function setPrimaryKey(array $primaryKey): TableDefinition
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    //endregion
}
