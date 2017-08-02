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
<script type="text/javascript">
    var mode = {$stripe_mode|escape:'htmlall':'UTF-8'};
</script>
{if !$ps_version15}
<div class="row">
	<div class="col-xs-12">
    {/if}
		<div class="payment_module cart-stripe-official" style="border: 1px solid #d6d4d4; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding-left: 15px; padding-right: 15px; background: #fbfbfb;">

			{* Classic Credit card form *}
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
			<div id="stripe-ajax-loader"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" />&nbsp; {l s='Transaction in progress, please wait.' mod='stripe_official'}</div>
			<form id="stripe-payment-form"{if isset($stripe_save_tokens_ask) && $stripe_save_tokens_ask && isset($stripe_credit_card)} style="display: none;"{/if}>

                <h3 class="stripe_title">{l s='Pay by card' mod='stripe_official'}</h3>

                <img class="cc-icon disable"  id="visa"       rel="visa"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-visa.png" />
                <img class="cc-icon disable"  id="mastercard" rel="masterCard" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-mastercard.png" />
                <img class="cc-icon disable"  id="amex"       rel="amex"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-amex.png" />
                {if $country_merchant == "us"}
                <img class="cc-icon disable"  id="discover"   rel="discover"   alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-discover.png" />
                <img class="cc-icon disable"  id="diners"     rel="diners"     alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-diners.png" />
                <img class="cc-icon disable"  id="jcb"        rel="jcb"        alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-jcb.png" />
                {/if}<br><br>

                <div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error|escape:'htmlall':'UTF-8'}{/if}</div>

                <!-- Used to display Element errors -->
                <div id="card-errors" role="alert"></div>


                <input type="hidden" id="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/>

                <div class="form-row">
                    <label for="card-element">
                        {l s='Cardholder\'s Name' mod='stripe_official'}
                    </label><label class="required"> </label>
                    <input name="cardholder-name" type="text"  autocomplete="off" class="stripe-name" data-stripe="name" value="{$customer_name|escape:'htmlall':'UTF-8'}"/>
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

                <button class="stripe-submit-button">{l s='Buy now' mod='stripe_official'}</button>

                <div class="clear"></div>
                <img class="powered_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/verified_by_visa.png"/>
                <img class="powered_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/mastercard_securecode.png"/>
                <img class="powered_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/powered_by_stripe.png"/>
			</form>
			<div id="stripe-translations">
				<span id="stripe-wrong-cvc">{l s='Wrong CVC.' mod='stripe_official'}</span>
				<span id="stripe-wrong-expiry">{l s='Wrong Credit Card Expiry date.' mod='stripe_official'}</span>
				<span id="stripe-wrong-card">{l s='Wrong Credit Card number.' mod='stripe_official'}</span>
				<span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='stripe_official'}</span>
				<span id="stripe-card-del">{l s='Your Credit Card has been successfully deleted, please enter a new Credit Card:' mod='stripe_official'}</span>
				<span id="stripe-card-del-error">{l s='An error occured while trying to delete this Credit card. Please contact us.' mod='stripe_official'}</span>
			</div>
		</div>

{if !$ps_version15}
    </div>
</div>
{/if}
<div id="modal_stripe"  class="modal" style="display: none">
            <div id="result_3d"> </div></div>


<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/jquery.the-modal.js"></script>
<link rel="stylesheet" href="{$module_dir|escape:'htmlall':'UTF-8'}views/css/the-modal.css" type="text/css" media="all">
<script type="text/javascript">
var ajaxUrlStripe = "{$ajaxUrlStripe|escape:'htmlall':'UTF-8'}";
var ps_version = {$ps_version15|escape:'htmlall':'UTF-8'};
var currency_stripe = "{$currency_stripe|escape:'htmlall':'UTF-8'}";
var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
var secure_mode = {$secure_mode|escape:'htmlall':'UTF-8'};
var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
var stripeLanguageIso = "{$stripeLanguageIso|escape:'htmlall':'UTF-8'}";
if (ps_version) {
    var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
}
var billing_address = {$billing_address nofilter};
{literal}

var stripe_isInit = false
var cardType;
if (StripePubKey && typeof stripe_v3 !== 'object') {
    var stripe_v3 = Stripe(StripePubKey);
}

(function() {
    initStripeOfficial();
})();

function initStripeOfficial() {
    stripe_isInit = true;
    // create elements
    var elements = stripe_v3.elements({locale:stripeLanguageIso});
    var card = elements.create('cardNumber', {
        style: {
            base: {
                fontSize: '15px',
            },
        }
    });
    var cvc = elements.create('cardCvc');
    var expire = elements.create('cardExpiry');

    // Add an instance of the card UI component into the `card-element` <div>
    card.mount('#cardNumber-element');
    cvc.mount('#cardCvc-element');
    expire.mount('#cardExpiry-element');

    card.addEventListener('change', function(event) {
        setOutcome(event);
        cardType = event.brand;
        if (cardType != "unknown") {
            if (cardType == "American Express")
                cardType = "amex";
            if (cardType == "Diners Club")
                cardType = "diners";
            if ($('.img-card').length > 0) {
                if ($('#img-'+cardType).length > 0) {
                    return false;
                } else {
                    $('.img-card').remove();
                }
            }

            var card_logo = document.createElement('img');
            if (ps_version)
                card_logo.src = baseDir + '/modules/stripe_official/views/img/cc-' + cardType.toLowerCase() +'.png';
            else
                card_logo.src = baseDir + 'modules/stripe_official/views/img/cc-' + cardType.toLowerCase() +'.png';
            card_logo.id = "img-"+cardType;
            card_logo.className = "img-card";
            $(card_logo).insertAfter('#cardNumber-element');
            $('#img-'+cardType).css({'margin-left': '-34px'});
            if (ps_version) {
                $('#img-'+cardType).css({
                    float: 'none',
                    'margin-bottom': '-5px'
                });
            }
            $('.cc-icon').removeClass('enable');
            $('.cc-icon').removeClass('disable');
            $('.cc-icon').each(function() {
                if ($(this).attr('rel') == cardType) {
                    $(this).addClass('enable');
                } else {
                    $(this).addClass('disable');
                }
            });
        } else {
            if ($('.img-card').length > 0) {
                $('.img-card').remove();
            }
            $('.cc-icon').removeClass('enable');
            $('.cc-icon:not(.disable)').addClass('disable');
        }
    });



    expire.addEventListener('change', function(event) {
        setOutcome(event);
    });

    cvc.addEventListener('change', function(event) {
        setOutcome(event);
    });

    function setOutcome(result) {

        $form = $('#stripe-payment-form');
        if (result.error) {
             $('#card-errors').show();
             $form.find('#card-errors').text(result.error.message).fadeIn(1000);
        } else {
             $('#card-errors').hide();
             $form.find('#card-errors').text()
        }
    }





    $('#stripe-payment-form').submit(function (event) {


        var $form = $(this);
        if (!StripePubKey) {
            $('#card-errors').show();
            $form.find('#card-errors').text($('#stripe-no_api_key').val()).fadeIn(1000);
            return false;
        }

        var owner_info = {
                address: {
                    line1: billing_address.line1,
                    line2: billing_address.line2,
                    city: billing_address.city,
                    postal_code: billing_address.zip_code,
                    country: billing_address.country
                },
                name: $('.stripe-name').val(),
                phone: billing_address.phone,
                email: billing_address.email,
        };

        for (var key in owner_info) {
            if (key == 'phone' && (!owner_info.phone || owner_info.phone == "" || owner_info.phone == "undefined")) {
                delete owner_info.phone;
            }
        }

        /* Disable the submit button to prevent repeated clicks */
       // $('.stripe-submit-button').attr('disabled', 'disabled');
        $('#card-errors').hide();
        $('#stripe-payment-form').hide();
        $('#stripe-ajax-loader').show();

        stripe_v3.createSource(card, {owner: owner_info}).then(function(result) {
            console.log(result);
            if (result.error) {
                $('#stripe-payment-form').show();
                $('#stripe-ajax-loader').hide();
                $('#stripe-payment-form #card-errors').show();
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                stripeSourceHandler(result.source);
            }
        });

        function stripeSourceHandler(response) {
            if (secure_mode && typeof response.card.three_d_secure != 'undefined' && response.card.three_d_secure != "not_supported") {
                stripe_v3.createSource({
                    type: 'three_d_secure',
                    amount: amount_ttl,
                    currency: currency_stripe,
                    three_d_secure: {
                        card: response.id
                    },
                    owner: owner_info,
                    redirect: {
                        return_url: baseDir+"modules/stripe_official/confirmation_3d.php"
                    }
                }).then(on3DSSource);
            } else {
                createCharge(response);
            }
        }

        function callbackFunction3D(result) {
            $('#modal_stripe').modalStripe().close();
        }

        function on3DSSource(result) {
            response = result.source;
            if (response.status == "pending") {
                $('#modal_stripe').modalStripe({cloning: false, closeOnOverlayClick: false, closeOnEsc: false}).open();
                Stripe.setPublishableKey(StripePubKey);
                Stripe.threeDSecure.createIframe(response.redirect.url, result_3d, callbackFunction3D);
                $('#result_3d iframe').css({
                    height: '400px',
                    width: '100%'
                });
                Stripe.source.poll(
                        response.id,
                        response.client_secret,
                        function(status, source) {
                            if (source.status == "chargeable") {
                                $('#modal_stripe').modalStripe().close();
                                createCharge(source);
                            } else if (source.status == "failed") {
                                $('#result_3d iframe').remove();
                                $('#modal_stripe').modalStripe().close();
                                $('#stripe-ajax-loader').hide();
                                $('#stripe-payment-form').show();
                                $('#card-errors').show();
                                $form.find('#card-errors').text($('#stripe-card_declined').val()).fadeIn(1000);
                            }
                        }
                );
            } else if (response.status == "chargeable") {
                createCharge(response);
            } else if (response.status == "failed") {
                var cardType = stripe_v3.card.cardType($('.stripe-card-number').val());
                if (cardType == "American Express") {
                    createCharge();
                } else {
                    $('#stripe-ajax-loader').hide();
                    $('#stripe-payment-form').show();
                    $('#card-errors').show();
                    $form.find('#card-errors').text($('#stripe-3d_declined').val()).fadeIn(1000);
                }
            }
        }



        function createCharge(result) {
            if (typeof(result) == "undefined") {
                result = response;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxUrlStripe,
                data: {
                    stripeToken: result.id,
                    cardType: cardType,
                    cardHolderName: $('.stripe-name').val(),
                },
                success: function(data) {
                    if (data.code == '1') {
                        // Charge ok : redirect the customer to order confirmation page
                        location.replace(data.url);
                    } else {
                        //  Charge ko
                        $('#stripe-ajax-loader').hide();
                        $('#stripe-payment-form').show();
                        $('#card-errors').show();
                        $('#card-errors').text(data.msg).fadeIn(1000);
                        $('.stripe-submit-button').removeAttr('disabled');
                    }
                },
                error: function(err) {
                    // AJAX ko
                    $('#stripe-ajax-loader').hide();
                    $('#stripe-payment-form').show();
                    $('#card-errors').show();
                    $('#card-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
                    $('.stripe-submit-button').removeAttr('disabled');
                }
            });
        }
        return false;
    });

  /* Cards mode */
  var cards_numbers = {
    "visa" : "4242424242424242",
    "mastercard" : "5555555555554444",
    "discover" : "378282246310005",
    "amex" : "6011111111111117",
    "jcb" : "30569309025904" ,
    "diners" : "3530111333300000"
  };




  /* Catch callback errors */
  if ($('#card-errors').text()) {
    $('#card-errors').fadeIn(1000);
  }

  $('#stripe-payment-form input').keypress(function () {
    $('#card-errors').fadeOut(500);
  });
};


</script>
{/literal}

