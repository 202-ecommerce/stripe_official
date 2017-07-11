{*
* 2007-2017 PrestaShop
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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2017 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
{if $stripe_giropay}
    <div class="payment_module stripe-europe-payments" data-method="giropay">
        <a href="" title="{l s='Pay by Giropay' mod='stripe_official'}">
            <img id="giropay" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/giropay.png" alt="{l s='Pay by Giropay' mod='stripe_official'}" />
            {l s='Pay by Giropay' mod='stripe_official'}
        </a>
        <p><input type="hidden" class="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/></p>
    </div>
{/if}
{if $stripe_bancontact}
    <div class="payment_module stripe-europe-payments" data-method="bancontact">
        <a href="" title="{l s='Pay by Bancontact' mod='stripe_official'}">
            <img id="bancontact" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/bancontact.png" alt="{l s='Pay by Bancontact' mod='stripe_official'}" />
            {l s='Pay by Bancontact' mod='stripe_official'}
        </a>
        <p><input type="hidden" class="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/></p>
    </div>
{/if}
{if $stripe_ideal}
    <div class="payment_module stripe-europe-payments" data-method="ideal">
        <a href="" title="{l s='Pay by iDeal' mod='stripe_official'}">
            <img id="ideal" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ideal.png" alt="{l s='Pay by iDeal' mod='stripe_official'}" />
            {l s='Pay by iDeal' mod='stripe_official'}
        </a>
        <p><input type="hidden" class="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/></p>
    </div>
{/if}
{if $stripe_sofort && $show_sofort}
    <div class="payment_module stripe-europe-payments" data-method="sofort">
        <a href="" title="{l s='Pay by SOFORT' mod='stripe_official'}">
            <img id="sofort" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/sofort.png" alt="{l s='Pay by SOFORT' mod='stripe_official'}" />
            {l s='Pay by SOFORT' mod='stripe_official'}
        </a>
    <p><input type="hidden" class="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/></p>
    </div>
{/if}

<div id="modal-stripe-error" class="modal" style="display: none">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <p class="stripe-payment-europe-errors"></p>
</div>

<script type="text/javascript">
    var currency_stripe = "{$currency_stripe|escape:'htmlall':'UTF-8'}";
    var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
    var stripe_error_msg = "{l s='An error occured during the request. Please contact us' mod='stripe_official'}";
    var stripe_customer_name = "{$customer_name|escape:'htmlall':'UTF-8'}";
    var stripe_cart_id = {$stripe_cart_id|escape:'htmlall':'UTF-8'};
    var stripe_order_url = "{$stripe_order_url}";
    var stripe_country_iso_code = "{$stripe_country_iso_code|escape:'htmlall':'UTF-8'}";
    var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
    var stripe_customer_email = "{$stripe_customer_email|escape:'htmlall':'UTF-8'}";
    var stripe_failed = "{$stripe_failed|escape:'htmlall':'UTF-8'}";
    var stripe_err_msg = "{$stripe_err_msg|escape:'htmlall':'UTF-8'}";
    var verification_url = "{$verification_url|escape:'htmlall':'UTF-8'}";
</script>