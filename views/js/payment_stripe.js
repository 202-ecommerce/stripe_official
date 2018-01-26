/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

var stripe_isInit = false;
var cardType;
var stripe_v3;
$(document).ready(function() {
    if (!stripe_isInit && $('section#checkout-payment-step').hasClass('js-current-step')) {
        if (StripePubKey && typeof stripe_v3 !== 'object') {
            stripe_v3 = Stripe(StripePubKey);
        }
        initStripeOfficial();
    }
});



function initStripeOfficial() {
    stripe_isInit = true;

    $('.stripe-payment').parent().prev().find('input[name=payment-option]').addClass('stripe-official');

    $('#stripe-payment-form input').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

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
        if (typeof cardType != "undefined") {
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
            card_logo.src = baseDir + 'modules/stripe_official/views/img/cc-' + cardType.toLowerCase() +'.png';
            card_logo.id = "img-"+cardType;
            card_logo.className = "img-card";
            $(card_logo).insertAfter('#cardNumber-element');
            $('#img-'+cardType).css({'margin-left': '-34px'});

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


    $('#payment-confirmation button').click(function (event) {
        if ($('input[name=payment-option]:checked').hasClass('stripe-official')) {
            $('#stripe-payment-form').submit();
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });


    $('#stripe-payment-form').submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var $form = $(this);
        if (!StripePubKey) {
            $('#card-errors').show();
            $form.find('#card-errors').text($('#stripe-no_api_key').val()).fadeIn(1000);
            return false;
        }

        /* Disable the submit button to prevent repeated clicks */
        $('#payment-confirmation button[type=submit]').attr('disabled', 'disabled');
        $('#card-errors').hide();
        $('#stripe-payment-form').hide();
        $('#stripe-ajax-loader').show();

        var owner_info = {
            address: {
                line1: billing_address.line1,
                line2: billing_address.line2,
                city: billing_address.city,
                postal_code: billing_address.zip_code,
                country: billing_address.country
            },
            name: $('.stripe-name').val(),
            email: billing_address.email,
        };

        stripe_v3.createSource(card, {owner: owner_info}).then(function(result) {
            if (result.error) {
                $('#stripe-payment-form').show();
                $('#stripe-ajax-loader').hide();
                $('#stripe-payment-form #card-errors').show();
                $('#payment-confirmation button[type=submit]').removeAttr('disabled');
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
                            $('#payment-confirmation button[type=submit]').removeAttr('disabled');
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

        function callbackFunction3D(result) {
            $('#modal_stripe').modalStripe().close();
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
                        $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                    }
                },
                error: function(err) {
                    // AJAX ko
                    $('#stripe-ajax-loader').hide();
                    $('#stripe-payment-form').show();
                    $('#card-errors').show();
                    $('#card-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
                    $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                }
            });
        }
    });

     /* Catch callback errors */
    if ($('#card-errors').text()) {
        $('#card-errors').fadeIn(1000);
    }

    $('#stripe-payment-form input').keypress(function () {
        $('#card-errors').fadeOut(500);
    });
};

