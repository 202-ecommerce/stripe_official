<?php
/**
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
 *
 * @version   release/2.3.1
 */

namespace Stripe_officialClasslib\Extensions\ProcessLogger\Classes;

use ObjectModel;

class ProcessLoggerObjectModel extends ObjectModel
{
    /** @var string name of action */
    public $name;

    /** @var string Message to display */
    public $msg;

    /** @var string level (success|error|info|deprecated) */
    public $level;

    /** @var string Name of ObjectModel associated if needed */
    public $object_name;

    /** @var int|null Identifier of resource announced with ObjectModel if needed */
    public $object_id;

    /**
     * @var int|null
     */
    public $id_session;

    /** @var string Date */
    public $date_add;

    /**
     * @see \ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stripe_official_processlogger',
        'primary' => 'id_stripe_official_processlogger',
        'fields' => [
            'name' => [
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 100,
            ],
            'msg' => [
                'type' => ObjectModel::TYPE_HTML,
                'validate' => 'isGenericName',
            ],
            'level' => [
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 10,
            ],
            'object_name' => [
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 100,
            ],
            'object_id' => [
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsigned',
                'allow_null' => true,
            ],
            'id_session' => [
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isString',
                'allow_null' => true,
            ],
            'date_add' => [
                'type' => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
        ],
    ];
}
