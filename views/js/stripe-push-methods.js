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

stripePayment_isInit = false;
$(document).ready(function() {
    if (!stripePayment_isInit && $('section#checkout-payment-step').hasClass('js-current-step')) {
        initStripeOfficialGiropay();
    }
});

function initStripeOfficialGiropay() {
    stripePayment_isInit = true;

    var stripe_submit_button = document.getElementById('payment-confirmation');
    stripe_submit_button.addEventListener('click', function (e) {
        
        var method_stripe = $('input[name=payment-option]:checked').data('module-name');
        var methods_stripe = ["ideal", "giropay", "bancontact", "sofort"];
        if (methods_stripe.indexOf($('input[name=payment-option]:checked').data('module-name')) == -1) {
            return true;
        }
        
        e.preventDefault();
        e.stopPropagation();
        
        if (StripePubKey && typeof stripe_v3 !== 'object') {
            var stripe_v3 = Stripe(StripePubKey);
        }

        if (method_stripe == 'sofort') {
            var method_info  = {
                country: $('#sofort_country').val(),
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
            currency: "eur",
            metadata: {
                cart_id: stripe_cart_id,
                email: stripe_customer_email,
                verification_url: verification_url,
            },
            owner: {
                name: stripe_customer_name,
            },
            redirect: {
                return_url: stripe_order_url,
            }
        };
        source_params[method_stripe] = method_info;
        stripe_v3.createSource(source_params).then(function(response) {
            if (response.error) {
                $('#modal-stripe-error').modalStripe({cloning: true, closeOnOverlayClick: true, closeOnEsc: true}).open();
                $('.stripe-payment-europe-errors').show().text(response.error.message).fadeIn(1000);
                $('#modal-stripe-error').parent().css({'z-index': 90000000000});
            } else {
                window.location.replace(response.source.redirect.url);
            }
        });
    });

    if (typeof stripe_failed != "undefined" && stripe_failed) {
        $('#modal-stripe-error').modalStripe({cloning: true, closeOnOverlayClick: true, closeOnEsc: true}).open();
        $('#modal-stripe-error').parent().css({'z-index': 90000000000});
        if (stripe_err_msg)
            $('.stripe-payment-europe-errors').show().text(stripe_err_msg).fadeIn(1000);
        else
            $('.stripe-payment-europe-errors').show().text(stripe_error_msg).fadeIn(1000);
    }

    $('#modal-stripe-error .close').click(function() {
        $('#modal-stripe-error').modalStripe().close();
    });

}