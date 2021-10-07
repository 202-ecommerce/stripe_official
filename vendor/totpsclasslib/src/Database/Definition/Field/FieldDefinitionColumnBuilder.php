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

namespace Stripe_officialClasslib\Database\Definition\Field;

use ObjectModel;
use Tools;

class FieldDefinitionColumnBuilder
{
    public function buildFieldDefinition(FieldDefinition $fieldDefinition)
    {
        $name = $fieldDefinition->getName();
        $constraints = $fieldDefinition->getDefinition();
        $column = "`$name` ";
        if (empty($constraints['values'])) {
            switch ($constraints['type']) {
                case ObjectModel::TYPE_BOOL:
                    $column .= 'TINYINT(1) UNSIGNED';
                    break;
                case ObjectModel::TYPE_DATE:
                    $column .= 'DATETIME';
                    break;
                case ObjectModel::TYPE_FLOAT:
                    $column .= 'DECIMAL' . (
                        isset($constraints['size'], $constraints['scale'])
                            ? "({$constraints['size']},{$constraints['scale']})"
                            : ''
                        );
                    break;
                case ObjectModel::TYPE_HTML:
                    $length = isset($constraints['size']) ? $constraints['size'] : null;
                    $length = isset($length['max']) ? $length['max'] : $length;
                    if ($length >= 65535) {
                        $column .= $length ? "TEXT($length)" : 'TEXT';
                    } else {
                        $column .= 'MEDIUMTEXT';
                    }
                    break;
                case ObjectModel::TYPE_INT:
                    $column .= 'INT(10)' . (
                        !empty($constraints['validate'])
                        && strpos(Tools::strtolower($constraints['validate']), 'unsigned')
                            ? ' UNSIGNED'
                            : ' SIGNED'
                        );
                    break;
                case ObjectModel::TYPE_STRING:
                    $length = isset($constraints['size']) ? $constraints['size'] : 255;
                    $length = isset($length['max']) ? $length['max'] : $length;
                    $column .= "VARCHAR($length)";
                    break;
                default:
                    throw new \PrestaShopException("Missing type constraint definition for field $name");
            }
        }

        if (!empty($constraints['values'])) {
            $column .= " ENUM('" . implode("','", $constraints['values']) . "')";
        }

        if (empty($constraints['allow_null']) || isset($constraints['default']) || !empty($constraints['required'])) {
            $column .= ' NOT NULL';
        }

        if (isset($constraints['default'])) {
            $column .= " DEFAULT '" . addslashes($constraints['default']) . "'";
        }

        if (!empty($constraints['primary'])) {
            $column .= ' AUTO_INCREMENT';
        }

        return $column;
    }
}
