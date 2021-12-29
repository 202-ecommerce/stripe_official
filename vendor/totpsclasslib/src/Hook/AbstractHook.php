<?php

namespace Stripe_officialClasslib\Hook;


use Stripe_officialClasslib\Extensions\AbstractModuleExtension;
use Stripe_officialClasslib\Module;
use Stripe_officialClasslib\Utils\Translate\TranslateTrait;

abstract class AbstractHook
{
    use TranslateTrait;
    const AVAILABLE_HOOKS = array();

    /**
     * @var Module
     */
    protected $module;

    /**
     * AbstractExtensionHook constructor.
     * @param Module $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Get all available hooks for current object
     * @return array
     */
    public function getAvailableHooks()
    {
        return static::AVAILABLE_HOOKS;
    }

    /**
     * Remove first 4 letters of hook function and replace the first letter by lower case
     * TODO maybe we should delete this function, because it isn't used
     * @param $functionName
     * @return string
     */
    protected function getHookNameFromFunction($functionName)
    {
        return lcfirst(substr($functionName, 4, strlen($functionName)));
    }
}