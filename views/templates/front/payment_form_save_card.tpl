{*
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
*}

<form class="stripe-payment-form save_card" action="">
    {if $prestashop_version == '1.7'}
        <input type="hidden" name="stripe-payment-method" value="card" data-id_payment_method="{$id_payment_method|escape:'htmlall':'UTF-8'}">
    {else}
        <p>{l s='Pay by card:' mod='stripe_official'} {$brand|escape:'htmlall':'UTF-8'} **** **** **** {$last4|escape:'htmlall':'UTF-8'}</p>
        <button class="stripe-submit-button" data-method="card" data-id_payment_method="{$id_payment_method|escape:'htmlall':'UTF-8'}">{l s='Buy now' mod='stripe_official'}</button>
    {/if}
</form>
