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

var stripe_isPaymentRequestInit = false;
var cardType;
var stripe_request_api;
var paymentRequest;
var elements_pr;
var prButton;

$(document).ready(function() {
    if (!stripe_isPaymentRequestInit) {
        if (StripePubKey && typeof stripe_request_api !== 'object') {
            stripe_request_api = Stripe(StripePubKey);
        }
        initPaymentRequestButtons();

        prestashop.on('updateProduct', (data) => {
            prButton.unmount();
            setTimeout(function(){
                // $('#payment-request-button').addClass('StripeElement');
                prButton.mount('#payment-request-button');
            }, 500);
        });
    }
});

function initPaymentRequestButtons()
{
    if(typeof amount !== 'undefined' && amount != null) {
        amountTtl = amount;
    }

    if(typeof productPrice !== 'undefined') {
        amountTtl = Math.round(productPrice*100);
    }

    var id_customer;

    var obj = jQuery.parseJSON(carriersRequest);
    var shipping_options = new Array();
    var cmpt = 0;
    for (var prop in obj) {
        shipping_options[cmpt] = {
            id: obj[prop]['id_carrier'],
            label: obj[prop]['name'],
            detail: obj[prop]['delay'],
            amount: obj[prop]['price'],
        }
        cmpt++;
    }

    if(typeof productPayment != 'undefined' && productPayment === true) {
        paymentRequest = stripe_request_api.paymentRequest({
            country: stripeLanguageIso.toUpperCase(),
            currency: currencyStripe.toLowerCase(),
            total: {
                label: 'Amount',
                amount: amount_ttl,
            },
            requestPayerEmail: true,
            requestPayerName: true,
            requestShipping: true,
            // `shippingOptions` is optional at this point:
            shippingOptions: shipping_options,
        });
    } else {
        paymentRequest = stripe_request_api.paymentRequest({
            country: stripeLanguageIso.toUpperCase(),
            currency: currencyStripe.toLowerCase(),
            total: {
                label: 'Amount',
                amount: amount_ttl,
            },
        });
    }

    elements_pr = stripe_request_api.elements({locale:stripeLanguageIso});
    prButton = elements_pr.create('paymentRequestButton', {
        paymentRequest: paymentRequest,
    });

    // Check the availability of the Payment Request API first.
    paymentRequest.canMakePayment().then(function(result) {
        if (result) {
            prButton.mount('#payment-request-button');
        } else {
            document.getElementById('payment-request-button').style.display = 'none';
        }
    });

 	paymentRequest.on('source', function(ev) {
        if(typeof productPayment != 'undefined' && productPayment === true) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: paymentRequestUrlStripe,
                async: false,
                data: {
                    addressInfos: ev.shippingAddress,
                    carrierInfos: ev.shippingOption.id,
                    payerEmail: ev.payerEmail,
                    payerName: ev.payerName,
                    onToken: true,
                },
                success: function(data) {
                    console.log('success');

                    var owner_info = {
                        address: {
                            line1: ev.shippingAddress.addressLine[0],
                            line2: ev.shippingAddress.addressLine[1],
                            city: ev.shippingAddress.city,
                            postal_code: ev.shippingAddress.postalCode,
                            country: ev.shippingAddress.country
                        },
                        name: ev.source.owner.name,
                        phone: ev.shippingAddress.phone,
                        email: ev.payerEmail,
                    };

                    if (secure_mode == 1 && typeof ev.source.card.three_d_secure != 'undefined' && ev.source.card.three_d_secure != "not_supported") {
                        threeds_datas = ev;
                        stripe_request_api.createSource({
                            type: 'three_d_secure',
                            amount: data,
                            currency: currency_stripe,
                            three_d_secure: {
                                card: ev.source.id
                            },
                            owner: owner_info,
                            redirect: {
                                return_url: baseDir+"modules/stripe_official/confirmation_3d.php"
                            }
                        }).then(on3DSSource);
                    } else {
                        createCharge(ev);
                    }
                },
                error: function(err) {
                    console.log('error');
                }
            });
        } else {
            var owner_info = {
                address: {
                    line1: ev.source.owner.address.line1,
                    line2: ev.source.owner.address.line2,
                    city: ev.source.owner.address.city,
                    postal_code: ev.source.owner.address.postal_code,
                    country: ev.source.owner.address.country
                },
                name: ev.source.owner.name,
                phone: ev.source.owner.phone,
                email: ev.source.owner.email,
            };

            if (secure_mode == 1 && typeof ev.source.card.three_d_secure != 'undefined' && ev.source.card.three_d_secure != "not_supported") {
                threeds_datas = ev;
                stripe_request_api.createSource({
                    type: 'three_d_secure',
                    amount: amount_ttl,
                    currency: currency_stripe,
                    three_d_secure: {
                        card: ev.source.id
                    },
                    owner: owner_info,
                    redirect: {
                        return_url: baseDir+"modules/stripe_official/confirmation_3d.php"
                    }
                }).then(on3DSSource);
            } else {
                createCharge(ev);
            }
        }
    });

    if(typeof productPayment != 'undefined' && productPayment === true) {
        prButton.on('click', function(ev) {
            id_combination = $('#product-details').data('product').id_product_attribute;
            quantity = $('#product-details').data('product').quantity_wanted;
            idProduct = $('#product-details').data('product').id_product;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: paymentRequestUrlStripe,
                async: false,
                data: {
                    product: idProduct,
                    productCombination: id_combination,
                    quantity: quantity,
                    idCustomer: id_customer,
                },
                success: function(data) {
                    console.log('success');
                    paymentRequest.update({
                        total: {
                            label: 'Amount',
                            amount: data,
                        },
                    });
                },
                error: function(err) {
                    console.log('error');
                }
            });
        });
    }

    paymentRequest.on('shippingaddresschange', function(ev) {
        $.ajax({
            type: 'POST',
            dataType: 'text',
            url: paymentRequestUrlStripe,
            data: {
                address: ev.shippingAddress,
                shippingaddresschange: true,
            },
            success: function(data) {
                if(Number.isInteger(parseInt(data)) == true) {
                    ev.updateWith({
                        status: 'success',
                        currency: currencyStripe.toLowerCase(),
                        total: {
                            label: 'Amount',
                            amount: parseInt(data),
                        },
                    });
                } else {
                    console.log(data);
                    ev.updateWith({
                        status: 'fail',
                    });
                }
            },
            error: function(err) {
                console.log('error');
            }
        });
    });

    paymentRequest.on('shippingoptionchange', function(ev) {
        $.ajax({
            type: 'POST',
            dataType: 'text',
            url: paymentRequestUrlStripe,
            data: {
                carrier: ev.shippingOption.id,
                shippingoptionchange: true,
            },
            success: function(data) {
                ev.updateWith({
                    status: 'success',
                    currency: currencyStripe.toLowerCase(),
                    total: {
                        label: 'Amount',
                        amount: parseInt(data),
                    },
                });
            },
            error: function(err) {
                console.log('error');
            }
        });
    });
}

function callbackFunction3D(result) {
    $('#modal_stripe').modalStripe().close();
}

function on3DSSource(result) {
    response = result.source;
    if (response.status == "pending") {
        threeds_datas.complete('success');
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
                        createCharge(source, true);
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
        createCharge(response, true);
    } else if (response.status == "failed") {
        var cardType = stripe_request_api.card.cardType($('.stripe-card-number').val());
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

function createCharge(result, threeds=false) {
    if (typeof(result) == "undefined") {
        result = response;
    }

    if(threeds === true) {
        result = threeds_datas;
    }

    var singleProductPrice = 0;
    if(typeof productPayment != 'undefined' && productPayment === true) {
        singleProductPrice = Math.round(productPrice*100);
        productPage = true;

        var datas = {
            "stripeToken": result.source.id,
            "cardType": result.source.card.brand,
            "cardHolderName": result.source.owner.name,
            "singleProductPrice": singleProductPrice,
            "city": result.shippingAddress.city,
            "country": result.shippingAddress.country,
            "line1": result.shippingAddress.addressLine[0],
            "line2": result.shippingAddress.addressLine[1],
            "postcode": result.shippingAddress.postalCode,
            "recipient": result.shippingAddress.recipient,
            "phone": result.shippingAddress.phone,
            "carrier": result.shippingOption.id,
            "paymentRequest": productPage,
        };
    } else {
        productPage = false;

        var datas = {
            "stripeToken": result.source.id,
            "cardType": result.source.card.brand,
            "cardHolderName": result.source.owner.name,
            "singleProductPrice": singleProductPrice,
            "paymentRequest": productPage,
        };
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxUrlStripe,
        async: false,
        data: datas,
        success: function(data) {
            console.log('success');
            if (data.code == '1') {
                // Charge ok : redirect the customer to order confirmation page
                result.complete('success');
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
            console.log('error');
            console.log(datas);
            // AJAX ko
            $('#stripe-ajax-loader').hide();
            $('#stripe-payment-form').show();
            $('#card-errors').show();
            $('#card-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
            $('.stripe-submit-button').removeAttr('disabled');
        }
    });
}