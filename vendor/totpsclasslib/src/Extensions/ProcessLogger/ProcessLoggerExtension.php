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
namespace Stripe_officialClasslib\Extensions\ProcessLogger;

use Stripe_officialClasslib\Extensions\AbstractModuleExtension;
use Stripe_officialClasslib\Extensions\ProcessLogger\Controllers\Admin\AdminProcessLoggerController;
use Stripe_officialClasslib\Extensions\ProcessLogger\Classes\ProcessLoggerObjectModel;

class ProcessLoggerExtension extends AbstractModuleExtension
{
    public $name = 'process_logger';

    public $extensionAdminControllers = array(
        array(
            'name' => array(
                'en' => 'Logger Stripe_official',
                'fr' => 'Logger Stripe_official',
            ),
            'class_name' => 'AdminStripe_officialProcessLogger',
            'parent_class_name' => 'stripe_official',
            'visible' => true,
        ),
    );

    public $objectModels = array(
        ProcessLoggerObjectModel::class
    );
}