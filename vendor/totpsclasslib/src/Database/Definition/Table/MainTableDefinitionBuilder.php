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
use ObjectModel;

class MainTableDefinitionBuilder extends AbstractTableDefinitionBuilder
{
    protected function getColumns()
    {
        $fields = [];

        if (count($this->objectModelDefinition->getPrimary()) == 1) {
            $fields[] = new FieldDefinition($this->objectModelDefinition->getPrimary()[0], [
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
                'primary' => true,
            ]);
        } else {
            foreach ($this->objectModelDefinition->getPrimary() as $primaryKey) {
                $keyFound = false;
                foreach ($this->objectModelDefinition->getFields() as $fieldDefinition) {
                    if ($fieldDefinition->getName() == $primaryKey) {
                        $keyFound = true;
                        break;
                    }
                }
                if (!$keyFound) {
                    throw new \PrestaShopException(sprintf('Primary key %s not found in field definitions', $primaryKey));
                }
            }
        }

        $fieldsDefinitions = array_filter($this->objectModelDefinition->getFields(), function (FieldDefinition $fieldDefinition) {
            return (empty($fieldDefinition->getDefinition()['lang']) && empty($fieldDefinition->getDefinition()['shop']))
                || (isset($fieldDefinition->getDefinition()['shop']) && $fieldDefinition->getDefinition()['shop'] == 'both');
        });

        return array_merge($fields, $fieldsDefinitions);
    }

    /**
     * @param TableDefinition $tableDefinition
     *
     * @return AbstractTableDefinitionBuilder
     */
    protected function buildSpecificFields(TableDefinition $tableDefinition)
    {
        $tableDefinition->setName($this->objectModelDefinition->getDbPrefix() . $this->objectModelDefinition->getTable())
            ->setAlias('m')
            ->setIndexes($this->objectModelDefinition->getIndexes())
            ->setPrimaryKey($this->objectModelDefinition->getPrimary())
            ->setForeignKeys($this->objectModelDefinition->getAssociations());

        return $this;
    }
}
