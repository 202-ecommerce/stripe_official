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

use Stripe_officialClasslib\Extensions\AbstractModuleExtension;
use Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel;
use Stripe_officialClasslib\Extensions\ProcessLogger\Controllers\Admin\AdminProcessLoggerController;
use Configuration;

class ProcessLoggerExtension extends AbstractModuleExtension
{
    public $name = 'process_logger';

    public $extensionAdminControllers = [
        [
            'name' => [
                'en' => 'Logger Stripe_official',
                'fr' => 'Logger Stripe_official',
            ],
            'class_name' => 'AdminStripe_officialProcessLogger',
            'parent_class_name' => 'stripe_official',
            'visible' => true,
        ],
    ];

    public $objectModels = [
        ProcessLoggerObjectModel::class,
    ];

    const QUIET_MODE = 'STRIPE_OFFICIAL_PROCESS_LOGGER_QUIET_MODE';

    const ERASING_DISABLED = 'STRIPE_OFFICIAL_EXTLOGS_ERASING_DISABLED';

    const ERASING_DAYSMAX = 'STRIPE_OFFICIAL_EXTLOGS_ERASING_DAYSMAX';

    public function install()
    {
        Configuration::updateGlobalValue(self::QUIET_MODE, 0);
        Configuration::updateGlobalValue(self::ERASING_DISABLED, 0);
        Configuration::updateGlobalValue(self::ERASING_DAYSMAX, 5);

        return parent::install();
    }
}
