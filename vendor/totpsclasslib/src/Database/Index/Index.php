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

use Stripe_officialClasslib\Database\Index\IndexField;
use Stripe_officialClasslib\Database\Index\IndexType;

class Index
{
    /**
     * @var array<IndexField>
     */
    private $fields = [];

    private $type = IndexType::STANDARD;

    private $name;

    private $options = '';

    private $table;

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return Index
     */
    public function setFields(array $fields): Index
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Index
     */
    public function setType(string $type): Index
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Index
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return $this->options;
    }

    /**
     * @param string $options
     *
     * @return Index
     */
    public function setOptions(string $options): Index
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     *
     * @return Index
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    public function getIndex()
    {
        if (empty($this->getFields()) || empty($this->getTable())) {
            return null;
        }

        $type = $this->getType();
        $columns = implode(', ', array_map(function ($field) {
            return $field->getColumn();
        }, $this->getFields()));
        $name = $this->getName();
        if (empty($name)) {
            $name = 'ix_' . preg_replace('/[^A-Za-z0-9\-_]/', '', $columns);
        }
        $options = $this->getOptions();
        $table = $this->getTable();

        return "CREATE $type INDEX $name ON $table ($columns) $options";
    }

    public static function build($index, $table)
    {
        $indexObj = new Index();
        $indexObj->setTable($table);

        if (!empty($index['type'])) {
            $indexObj->setType($index['type']);
        }

        if (!empty($index['name'])) {
            $indexObj->setName($index['name']);
        }

        if (!empty($index['options'])) {
            $indexObj->setOptions($index['options']);
        }

        if (!empty($index['fields'])) {
            $fields = [];
            foreach ($index['fields'] as $field) {
                $fields[] = (new IndexField())->setColumn($field['column']);
            }
            $indexObj->setFields($fields);
        }

        return $indexObj;
    }
}
