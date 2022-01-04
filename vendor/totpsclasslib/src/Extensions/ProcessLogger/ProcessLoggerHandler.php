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

namespace Stripe_officialClasslib\Extensions\ProcessLogger;

use Stripe_officialClasslib\Extensions\ProcessMonitor\ProcessMonitorHandler;
use Configuration;
use Db;
use Hook;
use Tools;

class ProcessLoggerHandler
{
    /**
     * @var ProcessMonitorHandler
     *                            Instance of ProcessMonitorHandler
     */
    private static $process;

    /**
     * @var array logs
     */
    private static $logs = [];

    /**
     * Set process name and remove oldest logs
     *
     * @param ProcessMonitorHandler|null $process
     */
    public static function openLogger($process = null)
    {
        self::$process = $process;
        self::autoErasingLogs();
    }

    /**
     * @param string|null $msg
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function closeLogger($msg = null)
    {
        if (self::$process != null && false === empty($msg)) {
            self::logInfo($msg, self::$process->getProcessObjectModelName(), self::$process->getProcessObjectModelId()); // summary
        }
        self::saveLogsInDb();
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string|null $name
     * @param string $level
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function addLog($msg, $objectModel = null, $objectId = null, $name = null, $level = 'info')
    {
        $isQuietMode = Configuration::get(ProcessLoggerExtension::QUIET_MODE);
        if ($isQuietMode && $level == 'info') {
            return;
        }

        self::$logs[] = [
            'name' => pSQL($name),
            'msg' => pSQL($msg),
            'level' => pSQL($level),
            'object_name' => pSQL($objectModel),
            'object_id' => (int)$objectId,
            'date_add' => date('Y-m-d H:i:s'),
            'id_session' => self::getSessionId(),
        ];

        if (100 === count(self::$logs)) {
            self::saveLogsInDb();
        }
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function logSuccess($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process->getProcessName();
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'success');
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function logError($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process->getProcessName();
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'error');
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function logInfo($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process->getProcessName();
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'info');
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function logDeprecated($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process->getProcessName();
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'deprecated');
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function saveLogsInDb()
    {
        $result = true;
        if (false === empty(self::$logs) && self::getSkippingHooksResult()) {
            Hook::exec(
                'actionProcessLoggerSave',
                [
                    'logs' => &self::$logs,
                ],
                null,
                true
            );
            Hook::exec(
                'actionStripe_officialProcessLoggerSave',
                [
                    'logs' => &self::$logs,
                ],
                null,
                true
            );

            $result = Db::getInstance()->insert(
                'stripe_official_processlogger',
                self::$logs
            );

            if ($result) {
                self::$logs = [];
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public static function autoErasingLogs()
    {
        if (self::isAutoErasingEnabled()) {
            return Db::getInstance()->delete(
                'stripe_official_processlogger',
                sprintf(
                    'date_add <= NOW() - INTERVAL %d DAY',
                    self::getAutoErasingDelayInDays()
                )
            );
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function isAutoErasingEnabled()
    {
        return false === (bool)Configuration::get('STRIPE_OFFICIAL_EXTLOGS_ERASING_DISABLED');
    }

    /**
     * @return int
     */
    public static function getAutoErasingDelayInDays()
    {
        $numberOfDays = Configuration::get('STRIPE_OFFICIAL_EXTLOGS_ERASING_DAYSMAX');

        if (empty($numberOfDays) || false === is_numeric($numberOfDays)) {
            return 5;
        }

        return (int)$numberOfDays;
    }

    /**
     * Executes the hooks used to skip a ProcessLogger save. This will return
     * false if any module hooked to either 'actionSkipProcessLoggerSave' or
     * 'actionSkipStripe_officialProcessLoggerSave' returns false (weak comparison)
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    protected static function getSkippingHooksResult()
    {
        if (Hook::getIdByName('actionSkipProcessLoggerSave')) {
            $hookProcessLoggerReturnArray = Hook::exec(
                'actionSkipProcessLoggerSave',
                [
                    'logs' => self::$logs,
                ],
                null,
                true
            );

            if (!is_array($hookProcessLoggerReturnArray)) {
                return false;
            }

            if (!empty($hookProcessLoggerReturnArray)) {
                $hookReturn = array_reduce($hookProcessLoggerReturnArray, function ($and, $hookReturn) {
                    return $and && (bool)$hookReturn;
                });
                if (!$hookReturn) {
                    return false;
                }
            }
        }

        if (Hook::getIdByName('actionSkipStripe_officialProcessLoggerSave')) {
            $hookModuleProcessLoggerReturnArray = Hook::exec(
                'actionSkipStripe_officialProcessLoggerSave',
                [
                    'logs' => self::$logs,
                ],
                null,
                true
            );

            if (!is_array($hookModuleProcessLoggerReturnArray)) {
                return false;
            }

            if (!empty($hookModuleProcessLoggerReturnArray)) {
                $hookReturn = array_reduce($hookModuleProcessLoggerReturnArray, function ($and, $hookReturn) {
                    return $and && (bool)$hookReturn;
                });
                if (!$hookReturn) {
                    return false;
                }
            }
        }

        return true;
    }

    protected static function getSessionId()
    {
        $values = [];
        $remoteAddr = Tools::getRemoteAddr();
        $values[] = $remoteAddr;
        if (!empty($_SERVER['REQUEST_TIME'])) {
            $values[] = $_SERVER['REQUEST_TIME'];
        } elseif (!empty($_SERVER['REQUEST_TIME_FLOAT'])) {
            $values[] = $_SERVER['REQUEST_TIME_FLOAT'];
        }

        if (!empty($_SERVER['REMOTE_PORT'])) {
            $values[] = $_SERVER['REMOTE_PORT'];
        }

        return sprintf('%08x', abs(crc32(implode('', $values))));
    }
}
