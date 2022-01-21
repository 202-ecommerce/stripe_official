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

namespace Stripe_officialClasslib\Hook;

use Stripe_officialClasslib\Extensions\AbstractModuleExtension;
use Stripe_officialClasslib\Module;
use Stripe_officialClasslib\Utils\Translate\TranslateTrait;

abstract class AbstractHook
{
    use TranslateTrait;

    const AVAILABLE_HOOKS = [];

    /**
     * @var Module
     */
    protected $module;

    /**
     * AbstractExtensionHook constructor.
     *
     * @param Module $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Get all available hooks for current object
     *
     * @return array
     */
    public function getAvailableHooks()
    {
        return static::AVAILABLE_HOOKS;
    }

    /**
     * Remove first 4 letters of hook function and replace the first letter by lower case
     * TODO maybe we should delete this function, because it isn't used
     *
     * @param string $functionName
     *
     * @return string
     */
    protected function getHookNameFromFunction($functionName)
    {
        return lcfirst(substr($functionName, 4, strlen($functionName)));
    }
}
