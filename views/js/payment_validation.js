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

$(document).ready(function() {
    if (typeof stripe_source != "undefined" && stripe_source != ""
        && typeof stripe_client_secret != "undefined" && stripe_client_secret != "") {
        if (StripePubKey) {
            Stripe.setPublishableKey(StripePubKey);
        }
        $('#modal_stripe_waiting').modalStripe({cloning: false, closeOnOverlayClick: false, closeOnEsc: false}).open();
        $('#modal_stripe_waiting').parent().css({'z-index': 90000000000});
        $('#stripe-ajax-loader-europe').show();
        source_chargeable = false;
        Stripe.source.poll(
            stripe_source,
            stripe_client_secret,
            function(status, source) {
                if (source.status == "chargeable") {
                    source_chargeable = true;
                    createCharge(source);
                } else if (source.status == "failed") {
                    location.replace(return_order_page);
                } else if (source.status == "consumed" && !source_chargeable) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxUrlStripe,
                        data: {
                            stripeToken: source.id,
                            checkOrder: true,
                            cart_id: source.metadata.cart_id,
                        },
                        success: function(data) {
                            if (data.confirmation_url != 'undefined') {
                                location.replace(data.confirmation_url);
                            }
                            if (data.error_url != 'undefined') {
                                location.replace(data.error_url);
                            }
                        },
                        error: function(err) {
                        }
                    });
                }
            }
        );
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
                cardType: result.type,
                cardHolderName: result.owner.name,
            },
            success: function(data) {
                if (data.code == '1') {
                    // Charge ok : redirect the customer to order confirmation page
                    location.replace(data.url);
                } else {
                    location.replace(return_order_page+'&stripe_err_msg='+data.msg);
                }
            },
            error: function(err) {
                location.replace(return_order_page);
            }
        });
    }

    $('#modal-stripe-error .close').click(function() {
        $('#modal-stripe-error').modalStripe().close();
    });
});