<?php
/**
* 2011-2017 202 ecommerce
*
*  @author    202 ecommerce <contact@202-ecommerce.com>
*  @copyright 202 ecommerce
*/

use Tot\Tests\Entity\Module;
use Tot\Tests\TotTestCase;

/**
 * @desc API Client of Trusted Shops
 */
class TestAdmin extends TotTestCase
{
    /**
     * @desc: setup of Phpunit
     */
    public function setUp()
    {
        $this->stripe = Module::load('stripe_official');
        if (is_object($this->stripe->module) && !$this->stripe->module->isInstalled('stripe_official')) {
            $this->stripe->module->install();
        }
    }

    /**
     * @desc Test load module page and redirection to login page
     */
    public function testPageConfig()
    {
        //$_GET['controller'] = 'AdminModules';
        //$_GET['configure'] = 'stripe_official';
        //$_GET['token'] = '6c813dd7e398d06deb0d92b4d99dfd60';
        $context = Context::getContext();
        $controller = new AdminModulesController();
        $context->controller = $controller;
        $context->smarty->addTemplateDir([
            'default' => _PS_ROOT_DIR_ . '/bb/themes/default/template/',
        ]);
        unset($_POST);
        try {
            $display = $this->stripe->module->getContent();
            echo $display;
        } catch (Exception $ex) {
            $this->assertRegExp('/xxx/', $ex->getMessage());

            return;
        }
    }
}
