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
<form class="stripe-payment-form" id="stripe-card-payment">
    {if $applepay_googlepay == 'on'}
        <div id="stripe-payment-request-button"></div>

        {if isset($prestashop_version) && $prestashop_version == '1.7'}
            <div class="stripe-payment-request-button-warning modal fade">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="closer" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="modal-body">{l s='Please make sure you agreed to our Terms of Service before going any further' mod='stripe_official'}</div>
                    </div>
                </div>
            </div>
        {/if}

        <p class="card-payment-informations">{l s='Pay now with the card saved in your device by clicking on the button above or fill in your card details below and submit at the end of the page' mod='stripe_official'}</p>
    {/if}

    <input type="hidden" name="stripe-payment-method" value="card">
    <div class="stripe-error-message alert alert-danger">
      {if isset($stripeError)}<p>{$stripeError|escape:'htmlall':'UTF-8'}</p>{/if}
    </div>

    {if $stripe_reinsurance_enabled == 'on'}
        <div class="form-row">
            <div id="cards-logos">
                {if isset($stripe_payment_methods)}
                    {foreach from=$stripe_payment_methods item=stripe_payment_method}
                        <img class="card_logo" src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo_{$stripe_payment_method.name|escape:'htmlall':'UTF-8'}.png" />
                    {/foreach}
                {/if}
            </div>
            {if isset($stripe_cardholdername_enabled) && $stripe_cardholdername_enabled == 'on'}
                <div class="stripe-card-cardholdername">
                    <label for="card-element">
                        {l s='Cardholder\'s Name' mod='stripe_official'}
                    </label><label class="required"> </label>
                    <input name="cardholder-name" type="text"  autocomplete="off" class="stripe-name" data-stripe="name" value="{if isset($customer_name)}{$customer_name|escape:'htmlall':'UTF-8'}{/if}"/>
                </div>
            {/if}
            <label for="card-element">
                {l s='Card Number' mod='stripe_official'}
            </label><label class="required"> </label>
            <div id="stripe-card-number" class="field"></div>
            <div class="block-left stripe-card-expiry">
                <label for="card-element">
                    {l s='Expiry date' mod='stripe_official'}
                </label><label class="required"> </label>
                <div id="stripe-card-expiry" class="field"></div>
            </div>
            <div class="stripe-card-cvc">
                <label for="card-element">
                    {l s='CVC/CVV' mod='stripe_official'}
                </label><label class="required"> </label>
                <div id="stripe-card-cvc" class="field"></div>
            </div>
            {if isset($stripe_postcode_enabled) && $stripe_postcode_enabled != 'on'}
                <div class="stripe-card-postalcode">
                    <label for="card-element">
                        {l s='Postal code' mod='stripe_official'}
                    </label><label class="required"> </label>
                    <div id="stripe-card-postalcode" class="field"></div>
                </div>
            {/if}
        </div>
    {else}
        <div id="stripe-card-element" class="field"></div>
        {if isset($stripe_cardholdername_enabled) && $stripe_cardholdername_enabled == 'on'}
            <input name="cardholder-name" type="text"  autocomplete="off" id="stripe-card-cardholdername" class="stripe-name" data-stripe="name" value="{if isset($customer_name)}{$customer_name|escape:'htmlall':'UTF-8'}{/if}"/>
        {/if}
    {/if}

    {if $stripe_save_card == 'on'}
        {if $show_save_card === true}
            <div id="save_card">
                <p class="checkbox">
                    <input type="checkbox" name="stripe_save_card" id="stripe_save_card" value="1">
                    <label for="stripe_save_card" class="{if isset($prestashop_version) && $prestashop_version == '1.6'}label16{else}label{/if} ml-2">{l s='Save card for future purchases' mod='stripe_official'}</label><br/>
                    <span class="{if isset($prestashop_version) && $prestashop_version == '1.6'}label16{else}label{/if}">{l s='Your card details are protected using PCI DSS v3.2 security standards.' mod='stripe_official'}</span>
                </p>
            </div>
        {elseif $show_save_card === false}
            <span class="{if isset($prestashop_version) && $prestashop_version == '1.6'}label16{else}label{/if}">{l s='Your card details will be saved automatically for your next purchase.' mod='stripe_official'}</span><br/>
        {/if}
    {/if}

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
        <button class="stripe-submit-button" data-method="card">{l s='Buy now' mod='stripe_official'}</button>
    {/if}

    {if $stripe_reinsurance_enabled == 'on'}
        <div id="powered_by_stripe">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/powered_by_stripe.png" />
        </div>
    {/if}
</form>
