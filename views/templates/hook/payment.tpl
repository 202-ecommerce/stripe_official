{*
* 2007-2016 PrestaShop
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
*	@copyright	2007-2016 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="payment_module" style="border: 1px solid #d6d4d4; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding-left: 15px; padding-right: 15px; background: #fbfbfb;">
			<h3 class="stripe_title"><img alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/secure-icon.png" />{l s='Pay by credit card with ' mod='prestastripe'}<img alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/logo.png" width="110px"/></h3>
			{* Classic Credit card form *}
            <input type="hidden" id="stripe-incorrect_number" value="{l s='The card number is incorrect.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-invalid_number" value="{l s='The card number is not a valid credit card number.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-invalid_expiry_month" value="{l s='The card\'s expiration month is invalid.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-invalid_expiry_year" value="{l s='The card\'s expiration year is invalid.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-invalid_cvc" value="{l s='The card\'s security code is invalid.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-expired_card" value="{l s='The card has expired.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-incorrect_cvc" value="{l s='The card\'s security code is incorrect.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-incorrect_zip" value="{l s='The card\'s zip code failed validation.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-card_declined" value="{l s='The card was declined.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-missing" value="{l s='There is no card on a customer that is being charged.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-processing_error" value="{l s='An error occurred while processing the car.' mod='prestastripe'}"></input>
            <input type="hidden" id="stripe-rate_limit" value="{l s='An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.' mod='prestastripe'}"></input>

			<div id="stripe-ajax-loader"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" />&nbsp; {l s='Transaction in progress, please wait.' mod='prestastripe'}</div>
			<form action="#" id="stripe-payment-form"{if isset($stripe_save_tokens_ask) && $stripe_save_tokens_ask && isset($stripe_credit_card)} style="display: none;"{/if}>
				<div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error|escape:'htmlall':'UTF-8'}{/if}</div><a name="stripe_error" style="display:none"></a>
        <input type="hidden" id="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/>

                <div>
				<label>{l s='Cardholder Name' mod='prestastripe'}</label><br />
        <input type="text" style="width: 200px;" autocomplete="off" class="stripe-name" data-stripe="name" value="{$customer_name|escape:'htmlall':'UTF-8'}"/>
                <img class="payment-ok" src="/img/admin/enabled.gif">
                <img class="payment-ko" src="/img/admin/disabled.gif">
                </div>
                <div>
                    <label>{l s='Card Number' mod='prestastripe'}</label><br />
                    <input type="text" size="20" autocomplete="off" class="stripe-card-number" id="card_number" data-stripe="number" placeholder="&#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679;"/>
                    <img style="margin-left: -57px;" class="payment-ok" src="/img/admin/enabled.gif">
                    <img style="margin-left: -57px;" class="payment-ko" src="/img/admin/disabled.gif">
                </div>
				<div class="block-left">
					<label>{l s='Card Type' mod='prestastripe'}</label><br />
					{if $mode == 1}
						<p>{l s='Click on any of the credit card buttons below in order to fill automatically the required fields to submit a test payment.' mod='prestastripe'}</p>
					{/if}
					<img class="cc-icon disable"  id="visa"       rel="Visa"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-visa.png" />
					<img class="cc-icon disable"  id="mastercard" rel="MasterCard" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-mastercard.png" />
					<img class="cc-icon disable"  id="discover"   rel="Discover"   alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-discover.png" />
					<img class="cc-icon disable"  id="amex"       rel="Amex"       alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-amex.png" />
					<img class="cc-icon disable"  id="jcb"        rel="Jcb"        alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-jcb.png" />
					<img class="cc-icon disable"  id="diners"     rel="Diners"     alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-diners.png" />
				</div>
                <br />
                <div class="block-left">
                <label>{l s='Expiration (MM/YYYY)' mod='prestastripe'}</label><br />
                <select id="month" name="month" data-stripe="exp-month" class="stripe-card-expiry-month">
                    <option value="01">{l s='January' mod='prestastripe'}</option>
                    <option value="02">{l s='February' mod='prestastripe'}</option>
                    <option value="03">{l s='March' mod='prestastripe'}</option>
                    <option value="04">{l s='April' mod='prestastripe'}</option>
                    <option value="05">{l s='May' mod='prestastripe'}</option>
                    <option value="06">{l s='June' mod='prestastripe'}</option>
                    <option value="07">{l s='July' mod='prestastripe'}</option>
                    <option value="08">{l s='August' mod='prestastripe'}</option>
                    <option value="09">{l s='September' mod='prestastripe'}</option>
                    <option value="10">{l s='October' mod='prestastripe'}</option>
                    <option value="11">{l s='November' mod='prestastripe'}</option>
                    <option value="12">{l s='December' mod='prestastripe'}</option>
                </select>
                <span> / </span>
                <select id="year" name="year" data-stripe="exp-year" class="stripe-card-expiry-year">
                    {for $n_pp_year={'Y'|date} to {'Y'|date}+9}
                        <option value="{$n_pp_year|escape:'htmlall':'UTF-8'}">{$n_pp_year|escape:'htmlall':'UTF-8'}</option>
                    {/for}
                </select>
                </div>
				<div>
					<label>{l s='CVC' mod='prestastripe'}</label><br />
					<input type="text" size="7" autocomplete="off" data-stripe="cvc" class="stripe-card-cvc" placeholder="&#9679;&#9679;&#9679;"/>
                    <img class="payment-ok" src="/img/admin/enabled.gif">
                    <img class="payment-ko" src="/img/admin/disabled.gif">
                    <a href="javascript:void(0)" class="stripe-card-cvc-info" style="border: none;">
						{l s='What\'s this?' mod='prestastripe'}
						<div class="cvc-info">
						{l s='The CVC (Card Validation Code) is a 3 or 4 digit code on the reverse side of Visa, MasterCard and Discover cards and on the front of American Express cards.' mod='prestastripe'}
						</div>
					</a>
				</div>
				<div class="clear"></div>

				<br />
				<button type="submit" class="stripe-submit-button">{l s='Submit Payment' mod='prestastripe'}</button>
			</form>
			<div id="stripe-translations">
				<span id="stripe-wrong-cvc">{l s='Wrong CVC.' mod='prestastripe'}</span>
				<span id="stripe-wrong-expiry">{l s='Wrong Credit Card Expiry date.' mod='prestastripe'}</span>
				<span id="stripe-wrong-card">{l s='Wrong Credit Card number.' mod='prestastripe'}</span>
				<span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='prestastripe'}</span>
				<span id="stripe-card-del">{l s='Your Credit Card has been successfully deleted, please enter a new Credit Card:' mod='prestastripe'}</span>
				<span id="stripe-card-del-error">{l s='An error occured while trying to delete this Credit card. Please contact us.' mod='prestastripe'}</span>
			</div>
		</div>
	</div>
</div>
<div id="result_3d";"></div>




<script type="text/javascript">
var ps_version = {$ps_version15|escape:'htmlall':'UTF-8'};
var currency = "{$currency|escape:'htmlall':'UTF-8'}";
var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
var secure_mode = {$secure_mode|escape:'htmlall':'UTF-8'};
if (ps_version) {
    var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
}
{literal}
function lookupCardType(number)
{
  if (number.match(new RegExp('^4')) !== null) {
    return 'Visa';
  }
  if (number.match(new RegExp('^(34|37)')) !== null) {
    return 'Amex';
  }
  if (number.match(new RegExp('^5[1-5]')) !== null) {
    return 'MasterCard';
  }
  if (number.match(new RegExp('^6011')) !== null) {
    return 'Discover';
  }
  if (number.match(new RegExp('^(?:2131|1800|35[0-9]{3})[0-9]{3,}')) !== null) {
    return 'Jcb';
  }
  if (number.match(new RegExp('^3(?:0[0-5]|[68][0-9])[0-9]{4,}')) !== null) {
    return 'Diners';
  }
}
function cc_format(value) {
    var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    var matches = v.match(/\d{4,16}/g);
    var match = matches && matches[0] || '';
    var parts = [];
    for (i=0, len=match.length; i<len; i+=4) {
        parts.push(match.substring(i, i+4));
    }
    if (parts.length) {
        return parts.join(' ');
    } else {
        return value;
    }
}



$(document).ready(function() {

    document.getElementById('card_number').oninput = function() {
        this.value = cc_format(this.value);

        cardNmb = Stripe.card.validateCardNumber($('.stripe-card-number').val());
        if (cardNmb) {
            $(this).parent().find('.payment-ko').hide();
            $(this).parent().find('.payment-ok').show();
        } else {
            $(this).parent().find('.payment-ok').hide();
            $(this).parent().find('.payment-ko').show();
        }

        var cardType = Stripe.card.cardType(this.value);
        if (cardType != "Unknown") {
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
            card_logo.src = baseDir + 'modules/prestastripe/views/img/cc-' + cardType.toLowerCase() +'.png';
            card_logo.id = "img-"+cardType;
            card_logo.className = "img-card";
            $(card_logo).insertAfter('.stripe-card-number');
            $('#img-'+cardType).css({'margin-left': '-34px'});
            if (ps_version) {
                $('#img-'+cardType).css({
                    float: 'none',
                    'margin-bottom': '-6px'
                });
            }

        } else {
            if ($('.img-card').length > 0) {
                $('.img-card').remove();
            }

        }
    }

    if (ps_version) {
        $('.payment-ko, .payment-ok').css({
            float: 'none',
            'margin-bottom': '-4px'
        });
    }

    $('.stripe-name').on('focusout', function(){
        if($(this).val().length > 0){
            $(this).parent().find('.payment-ko').hide();
            $(this).parent().find('.payment-ok').show();
        }else{
            $(this).parent().find('.payment-ok').hide();
            $(this).parent().find('.payment-ko').show();
        }
    });

    $('.stripe-card-cvc').on('keyup', function(){
        validCVC = Stripe.card.validateCVC($('.stripe-card-cvc').val());
        if(validCVC){
            $(this).parent().find('.payment-ko').hide();
            $(this).parent().find('.payment-ok').show();
        }else{
            $(this).parent().find('.payment-ok').hide();
            $(this).parent().find('.payment-ko').show();
        }
    });

    // Get Stripe public key
    var StripePubKey = $('#stripe-publishable-key').val();
    Stripe.setPublishableKey(StripePubKey);



    $('#stripe-payment-form').submit(function (event) {

        var $form = $(this);
        var cardNmb = Stripe.card.validateCardNumber($('.stripe-card-number').val());
        var cvcNmb = Stripe.card.validateCVC($('.stripe-card-cvc').val());
        if (cvcNmb == false) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-invalid_cvc').val()).fadeIn(1000);
            return false;
        }
        if (cardNmb == false) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-incorrect_number').val()).fadeIn(1000);
            return false;
        }
        /* Disable the submit button to prevent repeated clicks */
        $('.stripe-submit-button').attr('disabled', 'disabled');
        $('.stripe-payment-errors').hide();
        $('#stripe-payment-form').hide();
        $('#stripe-ajax-loader').show();



        Stripe.card.createToken($form, function (status, response) {
            var $form = $('#stripe-payment-form');
            if (response.error) {
                // Show error on the form
                $('#stripe-ajax-loader').hide();
                $('#stripe-payment-form').show();
                $('.stripe-submit-button').removeAttr('disabled');

                var err_msg = $('#stripe-'+response.error.code).val();
                if (!err_msg || err_msg == "undefined" || err_msg == '')
                    err_msg = response.error.message;
                $form.find('.stripe-payment-errors').text(err_msg).fadeIn(1000);
            } else {
                if (secure_mode) {
                    Stripe.threeDSecure.create({
                        card: response.id,
                        amount: amount_ttl,
                        currency: currency,
                    }, function (status, response) {
                        if (response.status == "redirect_pending") {
                            Stripe.threeDSecure.createIframe(response.redirect_url, result_3d, callbackFunction3D);
                            $('#result_3d iframe').css({
                                height: '400px',
                                width: '100%'
                            });
                        } else if (response.status == "succeeded") {
                            createCharge();
                        }
                        //return false; exit;
                    });
                    function callbackFunction3D(result) {
                        if (result.status == "succeeded") {
                            // Send the token back to the server so that it can charge the card
                            createCharge();
                        } else {
                            $('#stripe-ajax-loader').hide();
                            $('#stripe-payment-form').show();
                            $('.stripe-payment-errors').show();
                            $form.find('.stripe-payment-errors').text($('#stripe-card_declined').val()).fadeIn(1000);
                            $('.stripe-submit-button').removeAttr('disabled');
                        }
                    }
                } else {
                    createCharge();
                }

                function createCharge() {

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: baseDir + 'modules/prestastripe/ajax.php',
                        data: {
                            stripeToken: response.id,
                            cardType: lookupCardType($('.stripe-card-number').val()),
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
                                $('.stripe-payment-errors').show();
                                $('.stripe-payment-errors').text(data.msg).fadeIn(1000);
                                $('.stripe-submit-button').removeAttr('disabled');
                            }
                        },
                        error: function(err) {
                            // AJAX ko
                            $('#stripe-ajax-loader').hide();
                            $('#stripe-payment-form').show();
                            $('.stripe-payment-errors').show();
                            $('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
                            $('.stripe-submit-button').removeAttr('disabled');
                        }
                    });
                }
            }
        });
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

  /* Test Mode All Card enable */
  var cards = ["visa", "mastercard", "discover", "amex", "jcb", "diners"];
  if (mode == 1) {
    $.each(cards, function(data) {
      $('#' + cards[data]).addClass('enable');
    });

    /* Auto Fill in Test Mode */
    $.each(cards_numbers, function(key, value) {
      $('#' + key).click(function()  {
        $('.stripe-card-number').val(value);
        $('.stripe-name').val('Joe Smith');
        $('.stripe-card-cvc').val(131);
        $('.stripe-card-expiry-year').val('2023');
      });
    });

  }

  /* Determine the Credit Card Type */
  $('.stripe-card-number').keyup(function () {
    if ($(this).val().length >= 2) {
      stripe_card_type = lookupCardType($('.stripe-card-number').val());
      $('.cc-icon').removeClass('enable');
      $('.cc-icon').removeClass('disable');
      $('.cc-icon').each(function() {
        if ($(this).attr('rel') == stripe_card_type) {
          $(this).addClass('enable');
        } else {
          $(this).addClass('disable');
        }
      });
    } else {
      $('.cc-icon').removeClass('enable');
      $('.cc-icon:not(.disable)').addClass('disable');
    }
  });

  // TODO : Seems useless ...
  /*$('#stripe-payment-form-cc').submit(function (event) {
    $('.stripe-payment-errors').hide();
    $('#stripe-payment-form-cc').hide();
    $('#stripe-ajax-loader').show();
    $('.stripe-submit-button-cc').attr('disabled', 'disabled'); /* Disable the submit button to prevent repeated clicks */
  /*});

  /* Catch callback errors */
  if ($('.stripe-payment-errors').text()) {
    $('.stripe-payment-errors').fadeIn(1000);
  }

  $('#stripe-payment-form input').keypress(function () {
    $('.stripe-payment-errors').fadeOut(500); 
  });
});
</script>
{/literal}

