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
stripePayment_isInit = false;
$(document).ready(function() {
    if (!stripePayment_isInit && $('section#checkout-payment-step').hasClass('js-current-step')) {
        initStripeOfficialGiropay();
    }
});

function initStripeOfficialGiropay() {
    stripePayment_isInit = true;

     var err_msg_payment = $('.payment-error-'+stripe_type_error).text(stripe_payment_error);

    $('input[name=payment-option][data-module-name='+stripe_type_error+']').closest('.payment-option').after(err_msg_payment);

    var stripe_submit_button = document.getElementById('payment-confirmation');
    stripe_submit_button.addEventListener('click', function (e) {

        e.preventDefault();
        e.stopPropagation();

        var method_stripe = $('input[name=payment-option]:checked').data('module-name');
        var methods_stripe = ["ideal", "giropay", "bancontact", "sofort"];
        if (methods_stripe.indexOf($('input[name=payment-option]:checked').data('module-name')) == -1) {
            return true;
        }
        //$('input[name=stripe_method]').parent().addClass(method_stripe);
        // Get Stripe public key
        var StripePubKey = $('input[name=publishableKey]').val();
        if (StripePubKey) {
            Stripe.setPublishableKey(StripePubKey);
        }


        if (method_stripe == 'sofort') {
            var method_info  = {
                country: 'IT',
                statement_descriptor: 'Prestashop cart id '+$('input[name=stripe_cart_id]').val(),
            };
        } else {
            var method_info  = {
                statement_descriptor: 'Prestashop cart id '+$('input[name=stripe_cart_id]').val(),
            };
        }
        source_params = {
            type: method_stripe,
            amount: $('input[name=amount_ttl]').val(),
            currency: "eur",
            owner: {
                name: $('input[name=customer_name]').val(),
            },
            redirect: {
                return_url: $('input[name=stripe_order_url]').val(),
            }
        };
        source_params[method_stripe] = method_info;
        Stripe.source.create(source_params, function (status, response) {
            if (response.status == "pending") {
                window.location.replace(response.redirect.url);
            } else {
                $('.stripe-payment-europe-errors-'+method_stripe).show();
                $('.stripe-payment-europe-errors-'+method_stripe).text(response.error.message).fadeIn(1000);
            }


        });
    });

    if ($('input[name=stripe_source]').val() && $('input[name=stripe_client_secret]').val()) {
        var StripePubKey = $('input[name=publishableKey]').val();
        if (StripePubKey) {
            Stripe.setPublishableKey(StripePubKey);
        }
        Stripe.source.poll(
            $('input[name=stripe_source]').val(),
            $('input[name=stripe_client_secret]').val(),
            function(status, source) {
                if (source.status == "chargeable") {
                    createCharge(source);
                } else if (source.status == "failed") {
                    $('.stripe-payment-europe-errors-'+source.type).show();
                    $('.stripe-payment-europe-errors-'+source.type).text(data.msg).fadeIn(1000);
                }
            }
        );
    }

    function createCharge(result) {
        if (typeof(result) == "undefined") {
            result = response;
        }
        $('#modal_stripe_waiting').modalStripe({cloning: false, closeOnOverlayClick: false, closeOnEsc: false}).open();
        $('#modal_stripe_waiting').parent().css({'z-index': 90000000000});
        $('#stripe-ajax-loader-europe').show();

        $('input[name=stripeToken]').val(result.id);
        $('input[name=stripe_method][value='+result.type+']').parent('form[id=payment-form]').submit();




        /*  $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxUrlStripe,
            data: {
                stripeToken: result.id,
                cardType: result.type,
            },
            success: function(data) {
                if (data.code == '1') {
                    // Charge ok : redirect the customer to order confirmation page
                    location.replace(data.url);
                } else {
                    $('#modal_stripe_waiting').modalStripe().close();
                    //  Charge ko
                    $('.stripe-payment-europe-errors-'+result.type).show();
                    $('.stripe-payment-europe-errors-'+result.type).text(data.msg).fadeIn(1000);
                }
            },
            error: function(err) {
                $('#modal_stripe_waiting').modalStripe().close();
                // AJAX ko
                $('.stripe-payment-europe-errors-'+result.type).show();
                $('.stripe-payment-europe-errors-'+result.type).text(stripe_error_msg).fadeIn(1000);
            }
        });*/
    }


}