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

use Stripe_officialClasslib\Hook\AbstractHook;
use Stripe_officialClasslib\Module;

abstract class AbstractHookDispatcher
{
    protected $hookClasses = [];

    protected $widgetClasses = [];

    /**
     * List of available hooks
     *
     * @var string[]
     */
    protected $availableHooks = [];

    /**
     * Hook classes
     */
    protected $hooks = [];

    protected $widgets = [];

    /**
     * Module
     *
     * @var Module
     */
    protected $module;

    /**
     * Init hooks
     *
     * @param Module $module
     */
    public function __construct($module)
    {
        $this->module = $module;

        foreach ($this->hookClasses as $hookClass) {
            /** @var AbstractHook $hook */
            $hook = new $hookClass($this->module);
            $this->availableHooks = array_merge($this->availableHooks, $hook->getAvailableHooks());
            $this->hooks[] = $hook;
        }

        foreach ($this->widgetClasses as $widgetClass) {
            /** @var AbstractWidget $widgetClass */
            $widget = new $widgetClass($this->module);
            $this->widgets[] = $widget;
        }
    }

    /**
     * Get available hooks
     *
     * @return string[]
     */
    public function getAvailableHooks()
    {
        return $this->availableHooks;
    }

    /**
     * Find hook or widget and dispatch it
     *
     * @param string $hookName
     * @param array $params
     *
     * @return mixed|void
     */
    public function dispatch($hookName, array $params = [])
    {
        $hookName = preg_replace('~^hook~', '', $hookName);
        $hookName = lcfirst($hookName);

        if (!empty($hookName)) {
            foreach ($this->hooks as $hook) {
                if (is_callable([$hook, $hookName])) {
                    return call_user_func([$hook, $hookName], $params);
                }
            }
        }

        foreach ($this->widgets as $widget) {
            if (!isset($params['action'])) {
                continue;
            }

            if (is_callable([$widget, $params['action']])) {
                return call_user_func([$widget, $params['action']], $hookName, $params);
            }
        }

        return null;
    }

    /**
     * Get hook classes
     *
     * @return array
     */
    public function getHookClasses()
    {
        return $this->hookClasses;
    }

    /**
     * Get widget classes
     *
     * @return array
     */
    public function getWidgetClasses()
    {
        return $this->widgetClasses;
    }
}
