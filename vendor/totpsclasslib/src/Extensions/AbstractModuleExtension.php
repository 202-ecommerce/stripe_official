<?php
namespace Stripe_officialClasslib\Extensions;

abstract class AbstractModuleExtension
{
    //region Fields

    public $name;

    public $module;

    public $objectModels = array();

    public $hooks = array();

    public $extensionAdminControllers = array();

    public $controllers = array();

    public $cronTasks = array(); //TODO

    //endregion

    /**
     * @param Module $module
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }
}