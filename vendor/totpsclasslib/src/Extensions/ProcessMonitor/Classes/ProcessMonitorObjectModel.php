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
 * @version   release/2.3.1
 */

namespace Stripe_officialClasslib\Extensions\ProcessMonitor\Classes;

use \Db;
use \DbQuery;
use \ObjectModel;

class ProcessMonitorObjectModel extends ObjectModel
{
    /** @var string name of process */
    public $name;

    /** @var int PHP's process ID associated with process */
    public $pid = null;

    /** @var string Some useful data about process execution */
    public $data;

    /** @var float */
    public $duration = 0;

    /** @var string Date */
    public $last_update;

    /** @var string Boolean */
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'stripe_official_processmonitor',
        'primary'      => 'id_stripe_official_processmonitor',
        'fields'       => array(
            'name'     => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
                'unique'   => true,
                'required' => true,
            ),
            'pid' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
                'allow_null' => true,
            ),
            'data' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
            'duration' => array(
                'type' => ObjectModel::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
                'size' => 10,
                'scale' => 6,
            ),
            'active' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ),
            'last_update' => array(
                'type' => ObjectModel::TYPE_DATE,
            ),
        ),
    );

    /**
     * @param $name
     *
     * @return mixed
     */
    public function findOneByName($name)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(self::$definition['table'], 'p');
        $query->where('p.name = \''.pSQL($name).'\'');

        $data = Db::getInstance()->getRow($query);

        if (empty($data)) {
            return $this;
        }

        $this->hydrate($data);

        return $this;
    }
}
