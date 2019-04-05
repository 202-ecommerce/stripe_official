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
 * @version   release/2.1.0
 */

namespace Stripe_officialClasslib\Extensions\ProcessLogger\Controllers\Admin;

use Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel;

use \Db;
use \Tools;
use \Configuration;

class AdminProcessLoggerController extends \ModuleAdminController
{
    /** @var bool $bootstrap Active bootstrap for Prestashop 1.6 */
    public $bootstrap = true;

    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel';

    /** @var string Associated table name */
    public $table = 'stripe_official_processlogger';

    /** @var string|false Object identifier inside the associated table */
    public $identifier = 'id_stripe_official_processlogger';

    /** @var string Default ORDER BY clause when $_orderBy is not defined */
    protected $_defaultOrderBy = 'id_stripe_official_processlogger';

    /** @var string Default ORDER WAY clause when $_orderWay is not defined */
    protected $_defaultOrderWay = 'DESC';

    /** @var bool List content lines are clickable if true */
    protected $list_no_link = true;

    public $multishop_context = 0;

    /**
     * @see AdminController::__construct()
     */
    public function __construct()
    {
        parent::__construct();

        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->l('Delete selected', 'AdminProcessLoggerController'),
                'confirm' => $this->module->l(
                    'Would you like to delete the selected items?',
                    'AdminProcessLoggerController'
                ),
            )
        );

        $this->fields_list = array(
            'id_stripe_official_processlogger' => array(
                'title'  => $this->module->l('ID', 'AdminProcessLoggerController'),
                'align'  => 'center',
                'class'  => 'fixed-width-xs',
                'search' => true,
            ),
            'name'                      => array(
                'title' => $this->module->l('Name', 'AdminProcessLoggerController'),
            ),
            'msg'                       => array(
                'title' => $this->module->l('Message', 'AdminProcessLoggerController'),
            ),
            'level'                     => array(
                'title'    => $this->module->l('Level', 'AdminProcessLoggerController'),
                'callback' => 'getLevel',
            ),
            'object_name'               => array(
                'title' => $this->module->l('Object Name', 'AdminProcessLoggerController'),
            ),
            'object_id'                 => array(
                'title'    => $this->module->l('Object ID', 'AdminProcessLoggerController'),
                'callback' => 'getObjectId',
            ),
            'date_add'                  => array(
                'title' => $this->module->l('Date', 'AdminProcessLoggerController'),
            ),
        );

        $this->fields_options = array(
            'processLogger' => array(
                'image'       => '../img/admin/cog.gif',
                'title'       => $this->module->l('Process Logger Settings', 'AdminProcessLoggerController'),
                'description' => $this->module->l(
                    'Here you can change the default configuration for this Process Logger',
                    'AdminProcessLoggerController'
                ),
                'fields'      => array(
                    'STRIPE_OFFICIAL_EXTLOGS_ERASING_DISABLED' => array(
                        'title'        => $this->module->l(
                            'Disable auto erasing',
                            'AdminProcessLoggerController'
                        ),
                        'hint'         => $this->module->l(
                            'If disabled, logs will be automatically erased after the delay',
                            'AdminProcessLoggerController'
                        ),
                        'validation'   => 'isBool',
                        'cast'         => 'intval',
                        'type'         => 'bool',
                    ),
                    'STRIPE_OFFICIAL_EXTLOGS_ERASING_DAYSMAX' => array(
                        'title'        => $this->module->l(
                            'Auto erasing delay (in days)',
                            'AdminProcessLoggerController'
                        ),
                        'hint'         => $this->module->l(
                            'Choose the number of days you want to keep logs in database',
                            'AdminProcessLoggerController'
                        ),
                        'validation'   => 'isInt',
                        'cast'         => 'intval',
                        'type'         => 'text',
                        'defaultValue' => 5,
                    ),
                ),
                'submit'      => array(
                    'title' => $this->module->l('Save', 'AdminProcessLoggerController'),
                    'name' => 'submitSaveConf'),
            ),
        );
    }

    /**
     * @param $echo string Value of field
     * @param $tr array All data of the row
     * @return string
     */
    public function getObjectId($echo, $tr)
    {
        unset($tr);
        return empty($echo) ? '' : $echo;
    }

    /**
     * @param $echo string Value of field
     * @param $tr array All data of the row
     * @return string
     */
    public function getLevel($echo, $tr)
    {
        unset($tr);
        switch ($echo) {
            case 'info':
                $echo = '<span class="badge badge-info">'.$echo.'</span>';
                break;
            case 'success':
                $echo = '<span class="badge badge-success">'.$echo.'</span>';
                break;
            case 'error':
                $echo = '<span class="badge badge-danger">'.$echo.'</span>';
                break;
        }
        return $echo;
    }

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }

    /**
     * @see AdminController::initToolbar()
     */
    public function initToolbar()
    {
        parent::initToolbar();
        // Remove the add new item button
        unset($this->toolbar_btn['new']);
        $this->toolbar_btn['delete'] = array(
            'short' => 'Erase',
            'desc' => $this->module->l('Erase all'),
            'js' => 'if (confirm(\''.
                $this->module->l('Are you sure?', 'AdminProcessLoggerController').
                '\')) document.location = \''.self::$currentIndex.'&amp;token='.$this->token.'&amp;action=erase\';'
        );
    }

    /**
     * Delete all logs
     *
     * @return bool
     */
    public function processErase()
    {
        $result = Db::getInstance()->delete($this->table);

        if ($result) {
            $this->confirmations[] = $this->module->l('All logs has been erased', 'AdminProcessLoggerController');
        }

        return $result;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSaveConf')) {
            return $this->saveConfiguration();
        }

        return parent::postProcess();
    }

    public function saveConfiguration()
    {
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $extlogs_erasing_daysmax = Tools::getValue('STRIPE_OFFICIAL_EXTLOGS_ERASING_DAYSMAX');
            $extlogs_erasing_disabled = Tools::getValue('STRIPE_OFFICIAL_EXTLOGS_ERASING_DISABLED');
            
            Configuration::updateValue(
                'STRIPE_OFFICIAL_EXTLOGS_ERASING_DISABLED',
                ($extlogs_erasing_disabled ? true : false),
                false,
                null,
                $shop['id_shop']
            );

            if (!is_numeric($extlogs_erasing_daysmax)) {
                $this->errors[] = $this->module->l(
                    'You must specify a valid \"Auto erasing delay (in days)\" number.',
                    'AdminProcessLoggerController'
                );
            } else {
                Configuration::updateValue(
                    'STRIPE_OFFICIAL_EXTLOGS_ERASING_DAYSMAX',
                    $extlogs_erasing_daysmax,
                    false,
                    null,
                    $shop['id_shop']
                );
                $this->confirmations[] = $this->module->l(
                    'Log parameters are successfully updated!',
                    'AdminProcessLoggerController'
                );
            }
        }

        return true;
    }
}
