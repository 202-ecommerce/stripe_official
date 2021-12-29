<?php

namespace Stripe_officialClasslib\Hook;

use Stripe_officialClasslib\Hook\AbstractHook;
use Stripe_officialClasslib\Module;

abstract class AbstractHookDispatcher
{
    protected $hookClasses = array();

    protected $widgetClasses = array();

    /**
     * List of available hooks
     *
     * @var string[]
     */
    protected $availableHooks = array();

    /**
     * Hook classes
     *
     */
    protected $hooks = array();

    protected $widgets = array();

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
    public function dispatch($hookName, array $params = array())
    {
        $hookName = preg_replace('~^hook~', '', $hookName);

        if (!empty($hookName)) {
            foreach ($this->hooks as $hook) {
                if (is_callable(array($hook, $hookName))) {
                    return call_user_func(array($hook, $hookName), $params);
                }
            }
        }

        foreach ($this->widgets as $widget) {
            if (!isset($params['action'])) {
                continue;
            }

            if (is_callable(array($widget, $params['action']))) {
                return call_user_func(array($widget, $params['action']), $hookName, $params);
            }
        }

        return null;
    }

    /**
     * Get hook classes
     * @return array
     */
    public function getHookClasses()
    {
        return $this->hookClasses;
    }

    /**
     * Get widget classes
     * @return array
     */
    public function getWidgetClasses()
    {
        return $this->widgetClasses;
    }
}