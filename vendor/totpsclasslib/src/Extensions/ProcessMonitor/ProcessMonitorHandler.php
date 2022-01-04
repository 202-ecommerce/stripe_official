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

namespace Stripe_officialClasslib\Extensions\ProcessMonitor;

use Stripe_officialClasslib\Extensions\ProcessMonitor\Classes\ProcessMonitorObjectModel;

use \Tools;
use \Db;
use \ObjectModel;

class ProcessMonitorHandler
{
    /**
     * @var Stripe_officialProcessMonitorObjectModel $process
     */
    protected $process;

    /**
     * @var float $startTime microtime
     */
    public $startTime;

    /**
     * Lock process
     *
     * @param string $name
     * @return bool|mixed
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function lock($name)
    {
        $this->startTime = $this->microtimeFloat();
        $processMonitorObjectModel = new ProcessMonitorObjectModel();
        $this->process = $processMonitorObjectModel->findOneByName($name);
        if (empty($this->process->id)) {
            $this->process = new ProcessMonitorObjectModel();
            $this->process->name = $name;
            $this->process->data = Tools::jsonEncode(array());
            $this->process->date_add = date('Y-m-d H:i:s');
        }
        if (!empty($this->process->pid)) {
            $last_update = new \DateTime($this->process->last_update);
            $data_now = new \DateTime('NOW');
            $diff = $data_now->diff($last_update);
            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);
            if ($hours < 1) {
                return false;
            }
        }
        
        $this->process->last_update = date('Y-m-d H:i:s');
        $this->process->pid = getmypid();
        
        $this->date_upd = date('Y-m-d H:i:s');
        
        /**
         * We can't use the ObjectModel's "save", "add" or "update" methods.
         * PS will natively call ObjectModel hooks, using the class name of the
         * ObjectModel. On PS 1.6, the namespace is not escaped from the class name,
         * resulting in an invalid hook name, e.g. :
         * actionObjectShoppingfeedClasslib\Extensions\ProcessMonitor\ProcessMonitorObjectModelUpdateBefore
         */
        $definition = ObjectModel::getDefinition($this->process);
        if (empty($this->process->id)) {
            Db::getInstance()->insert(
                    $definition['table'],
                    $this->process->getFields(),
                    false
            );
            $this->process->id = Db::getInstance()->Insert_ID();
        } else {
            Db::getInstance()->update(
                    $definition['table'],
                    $this->process->getFields(),
                    '`'.pSQL($definition['primary']).'` = '.(int)$this->process->id,
                    0,
                    false
            );
        }

        return Tools::jsonDecode($this->process->data, true);
    }

    /**
     * UnLock process
     *
     * @param array $data
     * @return bool
     * @throws \PrestaShopException
     */
    public function unlock($data = array())
    {
        if (empty($this->process)) {
            return false;
        }

        if (false === empty($data)) {
            $this->process->data = Tools::jsonEncode($data);
        }
        $this->process->last_update = date('Y-m-d H:i:s');
        $endTime = $this->microtimeFloat();
        $duration = number_format(($endTime - $this->startTime), 3);
        $this->process->duration = $duration;
        $this->process->pid = null;

        /**
         * We can't use the ObjectModel's "save", "add" or "update" methods.
         * PS will natively call ObjectModel hooks, using the class name of the
         * ObjectModel. On PS 1.6, the namespace is not escaped from the class name,
         * resulting in an invalid hook name, e.g. :
         * actionObjectShoppingfeedClasslib\Extensions\ProcessMonitor\ProcessMonitorObjectModelUpdateBefore
         */
        $definition = ObjectModel::getDefinition($this->process);
        return Db::getInstance()->update(
                $definition['table'],
                $this->process->getFields(),
                '`'.pSQL($definition['primary']).'` = '.(int)$this->process->id,
                0,
                false
        );
    }

    /**
     * Get microtime in float value
     *
     * @return float
     */
    public function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * @return null|string
     */
    public function getProcessObjectModelName()
    {
        if (empty($this->process)) {
            return null;
        }
        return get_class($this->process);
    }

    /**
     * @return int|null
     */
    public function getProcessObjectModelId()
    {
        if (empty($this->process)) {
            return null;
        }
        return (int)$this->process->id;
    }

    /**
     * @return null|string
     */
    public function getProcessName()
    {
        if (empty($this->process)) {
            return null;
        }
        return $this->process->name;
    }
}
