/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

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

    $('.stripe-payment').parent().prev().find('input[name=payment-option]').addClass('stripe-official');

    //Put our input DOM element into a jQuery Object
    var jqDate = document.getElementById('card_expiry');

    //Bind keyup/keydown to the input
    $(jqDate).bind('keyup','keydown', function(e){
        var value_exp = $(jqDate).val();
        var v = value_exp.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        var matches = v.match(/\d{2,4}/g);

        //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
        if(e.which !== 8) {
            var numChars = value_exp.length;
            if(numChars === 2){
                var thisVal = value_exp;
                thisVal += '/';
                $(jqDate).val(thisVal);
            }
            if (numChars === 5)
                return false;
        }
    });


    document.getElementById('card_number').oninput = function() {
        this.value = cc_format(this.value);

        cardNmb = Stripe.card.validateCardNumber($('.stripe-card-number').val());

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
            card_logo.src = 'modules/stripe_official/views/img/cc-' + cardType.toLowerCase() +'.png';
            card_logo.id = "img-"+cardType;
            card_logo.className = "img-card";
            $(card_logo).insertAfter('.stripe-card-number');
            $('#img-'+cardType).css({'margin-left': '-34px'});
        } else {
            if ($('.img-card').length > 0) {
                $('.img-card').remove();
            }

        }
    }


    // Get Stripe public key
    var StripePubKey = $('#stripe-publishable-key').val();
    if (StripePubKey) {
        Stripe.setPublishableKey(StripePubKey);
    }


    $('#payment-confirmation button').click(function (event) {
        if ($('input[name=payment-option]:checked').hasClass('stripe-official')) {
            $('#stripe-payment-form').submit();
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });


    $('#stripe-payment-form').submit(function (event) {

        var $form = $(this);
        if (!StripePubKey) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-no_api_key').val()).fadeIn(1000);
            return false;
        }
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

        exp_month = $('.stripe-card-expiry').val();
        exp_month_calc = exp_month.substring(0, 2);
        exp_year = $('.stripe-card-expiry').val();
        exp_year_calc = "20" + exp_year.substring(3);

        Stripe.card.createToken({
            number: $('.stripe-card-number').val(),
            cvc: $('.stripe-card-cvc').val(),
            exp_month: exp_month_calc,
            exp_year: exp_year_calc,
            name: $('.stripe-name').val(),
            address_line1: billing_address.line1,
            address_line2: billing_address.line2,
            address_city: billing_address.city,
            address_zip: billing_address.zip_code,
            address_country: billing_address.country
        }, function (status, response) {
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
                if (secure_mode || response.card.three_d_secure.supported == "required") {
                    Stripe.threeDSecure.create({
                        card: response.id,
                        amount: amount_ttl,
                        currency: currency,
                    }, function (status, response) {
                        if (response.status == "redirect_pending") {
                            $('#modal_stripe').modalStripe({cloning: false, closeOnOverlayClick: false, closeOnEsc: false}).open();
                            Stripe.threeDSecure.createIframe(response.redirect_url, result_3d, callbackFunction3D);
                            $('#result_3d iframe').css({
                                height: '400px',
                                width: '100%'
                            });

                        } else if (response.status == "succeeded") {
                            createCharge(response);
                        } else if (response.status == "failed") {
                            var cardType = Stripe.card.cardType($('.stripe-card-number').val());
                            if (cardType == "American Express") {
                                createCharge();
                            } else {
                                $('#stripe-ajax-loader').hide();
                                $('#stripe-payment-form').show();
                                $('.stripe-payment-errors').show();
                                $form.find('.stripe-payment-errors').text($('#stripe-3d_declined').val()).fadeIn(1000);
                                $('.stripe-submit-button').removeAttr('disabled');
                            }
                        }
                        //return false; exit;
                    });
                    function callbackFunction3D(result) {
                        $('#modal_stripe').modalStripe().close();
                        if (result.status == "succeeded") {
                            // Send the token back to the server so that it can charge the card
                            createCharge(result);
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

                function createCharge(result) {
                    if (typeof(result) == "undefined") {
                        result = response;
                    }
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: baseDir + 'modules/stripe_official/ajax.php',
                        data: {
                            stripeToken: result.id,
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