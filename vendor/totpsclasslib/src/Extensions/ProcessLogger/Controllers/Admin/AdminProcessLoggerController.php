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

namespace Stripe_officialClasslib\Extensions\ProcessLogger\Controllers\Admin;

use Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;
use Configuration;
use Db;
use Shop;
use Tools;

class AdminProcessLoggerController extends \ModuleAdminController
{
    /** @var bool Active bootstrap for Prestashop 1.6 */
    public $bootstrap = true;

    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel';

    /** @var string Associated table name */
    public $table = 'stripe_official_processlogger';

    /** @var string|false Object identifier inside the associated table */
    public $identifier = 'id_stripe_official_processlogger';

    /** @var string Default ORDER BY clause when is not defined */
    protected $_defaultOrderBy = 'id_stripe_official_processlogger';

    /** @var string Default ORDER WAY clause when is not defined */
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

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected', 'AdminProcessLoggerController'),
                'confirm' => $this->module->l(
                    'Would you like to delete the selected items?',
                    'AdminProcessLoggerController'
                ),
            ],
        ];

        $this->fields_list = [
            'id_stripe_official_processlogger' => [
                'title' => $this->module->l('ID', 'AdminProcessLoggerController'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'search' => true,
            ],
            'name' => [
                'title' => $this->module->l('Name', 'AdminProcessLoggerController'),
            ],
            'msg' => [
                'title' => $this->module->l('Message', 'AdminProcessLoggerController'),
            ],
            'level' => [
                'title' => $this->module->l('Level', 'AdminProcessLoggerController'),
                'callback' => 'getLevel',
            ],
            'object_name' => [
                'title' => $this->module->l('Object Name', 'AdminProcessLoggerController'),
            ],
            'object_id' => [
                'title' => $this->module->l('Object ID', 'AdminProcessLoggerController'),
                'callback' => 'getObjectId',
            ],
            'id_session' => [
                'title' => $this->module->l('Session ID', 'AdminProcessLoggerController'),
            ],
            'date_add' => [
                'title' => $this->module->l('Date', 'AdminProcessLoggerController'),
            ],
        ];

        $this->fields_options = [
            'processLogger' => [
                'image' => '../img/admin/cog.gif',
                'title' => $this->module->l('Process Logger Settings', 'AdminProcessLoggerController'),
                'description' => $this->module->l(
                    'Here you can change the default configuration for this Process Logger',
                    'AdminProcessLoggerController'
                ),
                'fields' => [
                    ProcessLoggerExtension::QUIET_MODE => [
                        'title' => $this->module->l(
                            'Activate quiet mode',
                            'AdminProcessLoggerController'
                        ),
                        'hint' => $this->module->l(
                            'If quiet mode is activated, only success and error logs are saved. Logs with a level info are not saved.',
                            'AdminProcessLoggerController'
                        ),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ],
                    ProcessLoggerExtension::ERASING_DISABLED => [
                        'title' => $this->module->l(
                            'Disable auto erasing',
                            'AdminProcessLoggerController'
                        ),
                        'hint' => $this->module->l(
                            'If disabled, logs will be automatically erased after the delay',
                            'AdminProcessLoggerController'
                        ),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ],
                    ProcessLoggerExtension::ERASING_DAYSMAX => [
                        'title' => $this->module->l(
                            'Auto erasing delay (in days)',
                            'AdminProcessLoggerController'
                        ),
                        'hint' => $this->module->l(
                            'Choose the number of days you want to keep logs in database',
                            'AdminProcessLoggerController'
                        ),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'defaultValue' => 5,
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminProcessLoggerController'),
                    'name' => 'submitSaveConf',
                ],
            ],
        ];
    }

    /**
     * @param string $echo Value of field
     * @param array $tr All data of the row
     *
     * @return string
     */
    public function getObjectId($echo, $tr)
    {
        unset($tr);

        return empty($echo) ? '' : $echo;
    }

    /**
     * @param string $echo Value of field
     * @param array $tr All data of the row
     *
     * @return string
     */
    public function getLevel($echo, $tr)
    {
        unset($tr);
        switch ($echo) {
            case 'info':
                $echo = '<span class="badge badge-info">' . $echo . '</span>';
                break;
            case 'success':
                $echo = '<span class="badge badge-success">' . $echo . '</span>';
                break;
            case 'error':
                $echo = '<span class="badge badge-danger">' . $echo . '</span>';
                break;
            case 'deprecated':
                $echo = '<span class="badge badge-warning">' . $echo . '</span>';
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
        $this->toolbar_btn['delete'] = [
            'short' => 'Erase',
            'desc' => $this->module->l('Erase all'),
            'js' => 'if (confirm(\'' .
                $this->module->l('Are you sure?', 'AdminProcessLoggerController') .
                '\')) document.location = \'' . self::$currentIndex . '&amp;token=' . $this->token . '&amp;action=erase\';',
        ];
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
        $shops = \Shop::getShops(false, null, true);
        $shops[] = 0;
        foreach ($shops as $idShop) {
            $extlogsQuietMode = Tools::getValue(ProcessLoggerExtension::QUIET_MODE);
            $extlogsErasingDaysmax = Tools::getValue(ProcessLoggerExtension::ERASING_DAYSMAX);
            $extlogsErasingDisabled = Tools::getValue(ProcessLoggerExtension::ERASING_DISABLED);

            Configuration::updateValue(
                ProcessLoggerExtension::QUIET_MODE,
                (bool)$extlogsQuietMode,
                false,
                null,
                $idShop
            );

            Configuration::updateValue(
                ProcessLoggerExtension::ERASING_DISABLED,
                (bool)$extlogsErasingDisabled,
                false,
                null,
                $idShop
            );

            if (!is_numeric($extlogsErasingDaysmax)) {
                $this->errors[] = $this->module->l(
                    'You must specify a valid \"Auto erasing delay (in days)\" number.',
                    'AdminProcessLoggerController'
                );
            } else {
                Configuration::updateValue(
                    ProcessLoggerExtension::ERASING_DAYSMAX,
                    $extlogsErasingDaysmax,
                    false,
                    null,
                    $idShop
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
