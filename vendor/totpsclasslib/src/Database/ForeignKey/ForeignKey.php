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

use Stripe_officialClasslib\Database\ForeignKey\ReferenceOption;
use ObjectModel;
use PrestaShopException;

class ForeignKey
{
    //region Fields

    private $table;

    private $type = ObjectModel::HAS_MANY;

    private $association;

    private $object;

    private $field;

    private $onUpdate = ReferenceOption::CASCADE;

    private $onDelete = ReferenceOption::CASCADE;

    //endregion

    //region Get-Set

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
     * @return ForeignKey
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return ForeignKey
     */
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     *
     * @return ForeignKey
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     *
     * @return ForeignKey
     */
    public function setAssociation($association)
    {
        $this->association = $association;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     *
     * @return ForeignKey
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    /**
     * @param string $onUpdate
     *
     * @return ForeignKey
     */
    public function setOnUpdate(string $onUpdate)
    {
        $this->onUpdate = $onUpdate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * @param string $onDelete
     *
     * @return ForeignKey
     */
    public function setOnDelete(string $onDelete)
    {
        $this->onDelete = $onDelete;

        return $this;
    }

    //endregion

    public function getForeignKey()
    {
        $object = $this->getObject();
        $objectTable = _DB_PREFIX_ . $object::$definition['table'];

        return "
            ALTER TABLE {$this->getTable()}
            ADD FOREIGN KEY ({$this->getField()})
            REFERENCES {$objectTable} ({$object::$definition['primary']})
            ON DELETE {$this->getOnDelete()}
            ON UPDATE {$this->getOnUpdate()}
        ";
    }

    /**
     * @return ForeignKey
     *
     * @throws PrestaShopException
     */
    public function build($table, $association)
    {
        $this->setTable($table);

        if ($association['association']) {
            $this->setAssociation($association['association']);
        }

        if (isset($association['type'])) {
            $this->setType($association['type']);
        }

        if (isset($association['object'])) {
            $this->setObject($association['object']);
        } else {
            throw new PrestaShopException('Association should have an associated object');
        }

        if (isset($association['field'])) {
            $this->setField($association['field']);
        } else {
            throw new PrestaShopException('Association should have an associated field');
        }

        if (isset($association['on_delete'])) {
            $this->setOnDelete($association['on_delete']);
        }

        if (isset($association['on_update'])) {
            $this->setOnUpdate($association['on_update']);
        }

        return $this;
    }
}
