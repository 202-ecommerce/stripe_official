{*
* 2007-2018 PrestaShop
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
*	@copyright	2007-2018 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

{if $SSL}
<div class="row stripe-payment">
  <div class="col-xs-12">
    <div class="payment_module" style="border: 1px solid #d6d4d4; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding-left: 15px; padding-right: 15px; background: #fbfbfb;">
      <input type="hidden" id="stripe-incorrect_number" value="{l s='The card number is incorrect.' mod='stripe_official'}">
      <input type="hidden" id="stripe-invalid_number" value="{l s='The card number is not a valid credit card number.' mod='stripe_official'}">
      <input type="hidden" id="stripe-invalid_expiry_month" value="{l s='The card\'s expiration month is invalid.' mod='stripe_official'}">
      <input type="hidden" id="stripe-invalid_expiry_year" value="{l s='The card\'s expiration year is invalid.' mod='stripe_official'}">
      <input type="hidden" id="stripe-invalid_cvc" value="{l s='The card\'s security code is invalid.' mod='stripe_official'}">
      <input type="hidden" id="stripe-expired_card" value="{l s='The card has expired.' mod='stripe_official'}">
      <input type="hidden" id="stripe-incorrect_cvc" value="{l s='The card\'s security code is incorrect.' mod='stripe_official'}">
      <input type="hidden" id="stripe-incorrect_zip" value="{l s='The card\'s zip code failed validation.' mod='stripe_official'}">
      <input type="hidden" id="stripe-card_declined" value="{l s='The card was declined.' mod='stripe_official'}">
      <input type="hidden" id="stripe-missing" value="{l s='There is no card on a customer that is being charged.' mod='stripe_official'}">
      <input type="hidden" id="stripe-processing_error" value="{l s='An error occurred while processing the car.' mod='stripe_official'}">
      <input type="hidden" id="stripe-rate_limit" value="{l s='An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.' mod='stripe_official'}">
      <input type="hidden" id="stripe-3d_declined" value="{l s='The card doesn\'t support 3DS.' mod='stripe_official'}">
      <input type="hidden" id="stripe-no_api_key" value="{l s='There\'s an error with your API keys. If you\'re the administrator of this website, please go on the "Connection" tab of your plugin.' mod='stripe_official'}">
      <div id="stripe-ajax-loader"><img src="{$module_dir}views/img/ajax-loader.gif" alt="" /> {l s='Transaction in progress, please wait.' mod='stripe_official'}</div>
      <form id="stripe-payment-form" action="#">

        <h3 class="stripe_title">{l s='Pay by card' mod='stripe_official'}</h3>


        <div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error}{/if}</div>

        <!-- Used to display Element errors -->
        <div id="card-errors" role="alert"></div>


        <input type="hidden" id="stripe-publishable-key" value="{$publishableKey}"/>

        <div class="form-row">
          <label for="card-element">
            {l s='Cardholder\'s Name' mod='stripe_official'}
          </label><label class="required"> </label>
          <input name="cardholder-name" type="text"  autocomplete="off" class="stripe-name" data-stripe="name" value="{$customer_name}"/>
          <label for="card-element">
            {l s='Card Number' mod='stripe_official'}
          </label><label class="required"> </label>
          <div id="cardNumber-element">
            <!-- a Stripe Element will be inserted here. -->
          </div>
          <div class="block-left stripe-card-expiry">
            <label for="card-element">
              {l s='Expiry date' mod='stripe_official'}
            </label><label class="required"> </label>
            <div id="cardExpiry-element">
              <!-- a Stripe Element will be inserted here. -->
            </div>
          </div>
          <div class="stripe-card-cvc">
            <label for="card-element">
              {l s='CVC/CVV' mod='stripe_official'}
            </label><label class="required"> </label>
            <div id="cardCvc-element">
              <!-- a Stripe Element will be inserted here. -->
            </div>
          </div>

        </div>


        <div class="clear"></div>
        <img class="powered_stripe" alt="" src="{$module_dir}views/img/verified_by_visa.png"/>
        <img class="powered_stripe" alt="" src="{$module_dir}views/img/mastercard_securecode.png"/>
        <img class="powered_stripe" alt="" src="{$module_dir}views/img/powered_by_stripe.png"/>
      </form>
    </div>
  </div>
</div>
<div id="modal_stripe"  class="modal" style="display: none">
  <div id="result_3d"> </div></div>
<script type="text/javascript">
  var mode = {$stripe_mode};
  var currency_stripe = "{$currency_stripe}";
  var amount_ttl = {$amount_ttl};
  var secure_mode = {$secure_mode};
  var baseDir = "{$baseDir}";
  var billing_address = {$billing_address nofilter};
  var module_dir = "{$module_dir}";
  var ajaxUrlStripe = "{$ajaxUrlStripe}";
  var StripePubKey = "{$publishableKey}";
  var stripeLanguageIso = "{$stripeLanguageIso}";
</script>
{else}
<div class="row stripe-payment">
  <div class="col-xs-12">
    <div class="payment_module" style="border: 1px solid #d6d4d4; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding-left: 15px; padding-right: 15px; background: #fbfbfb;">
      <p class="alert alert-danger">{l s='SSL is off, please activate it.' mod='stripe_official'}</p>
    </div>
  </div>
</div>
{/if}