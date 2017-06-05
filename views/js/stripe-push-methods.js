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
stripeGiropay_isInit = false;
$(document).ready(function() {
    if (!stripeGiropay_isInit) {
        initStripeOfficialGiropay();
    }
});

function initStripeOfficialGiropay() {
    stripeGiropay_isInit = true;

    $(document).on('click', '.stripe-europe-payments', function(e){
        // Get Stripe public key
        var StripePubKey = $('#stripe-publishable-key').val();
        if (StripePubKey) {
            Stripe.setPublishableKey(StripePubKey);
        }
        var method_stripe = $(this).attr('data-method');
        e.preventDefault();
        e.stopPropagation();
        if (method_stripe == 'sofort') {
            var method_info  = {
                country: 'IT',
                statement_descriptor: 'Prestashop cart id '+stripe_cart_id,
            };
        } else {
            var method_info  = {
                statement_descriptor: 'Prestashop cart id '+stripe_cart_id,
            };
        }
        source_params = {
            type: method_stripe,
            amount: amount_ttl,
            currency: currency_stripe,
            owner: {
                name: stripe_customer_name,
            },
            redirect: {
                return_url: stripe_order_url,
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

    if (stripe_source && stripe_client_secret) {
        var StripePubKey = $('#stripe-publishable-key').val();
        if (StripePubKey) {
            Stripe.setPublishableKey(StripePubKey);
        }
        Stripe.source.poll(
            stripe_source,
            stripe_client_secret,
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
        $.ajax({
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
        });
    }


}