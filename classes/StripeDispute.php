<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) Stripe
 * @license   Commercial license
 */

use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class StripeDispute extends ObjectModel
{
    /** @var string */
    public $stripe_dispute_id;

    public function __construct($stripe_dispute_id = null)
    {
        $this->stripe_dispute_id = $stripe_dispute_id;
    }

    public function getAllDisputes()
    {
        $module = Module::getInstanceByName('stripe_official');

        $stripe = new \Stripe\StripeClient(
            $module->getSecretKey()
        );

        return $stripe->disputes->all();
    }

    public function orderHasDispute($id_charge)
    {
        $disputes = $this->getAllDisputes();

        foreach ($disputes->data as $dispute) {
            if ($dispute->charge == $id_charge) {
                return true;
            }
        }

        return false;
    }
}
