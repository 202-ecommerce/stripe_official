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
*   @author PrestaShop SA <contact@prestashop.com>
*   @copyright  2007-2019 PrestaShop SA
*   @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*   International Registered Trademark & Property of PrestaShop SA
*}

<form class="stripe-payment-form" id="stripe-card-payment">

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
        <h3 class="stripe_title">{l s='Pay by card' mod='stripe_official'}</h3>

        <img class="cc-icon disable"  id="visa"       rel="visa"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-visa.png" />
        <img class="cc-icon disable"  id="mastercard" rel="masterCard" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-mastercard.png" />
        <img class="cc-icon disable"  id="amex"       rel="amex"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-amex.png" />
    {/if}

    <input type="hidden" name="stripe-payment-method" value="card">
    <div class="stripe-error-message alert alert-danger"><p>{if isset($stripeError)}{$stripeError}{/if}</p></div>
    <div id="stripe-card-element" class="field"></div>

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
        <button class="stripe-submit-button" data-method="card">{l s='Buy now' mod='stripe_official'}</button>
    {/if}

    {if $applepay_googlepay == 'on'}
        <div id="stripe-payment-request-button"></div>
    {/if}
</form>
