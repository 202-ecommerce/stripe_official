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

$(document).ready(function() {
    if (!stripe_isPaymentRequestInit && $('section#checkout-payment-step').hasClass('js-current-step')) {
        if (StripePubKey && typeof stripe_request_api !== 'object') {
            stripe_request_api = Stripe(StripePubKey);
        }
        initPaymentRequestButtons();
    }
});

function initPaymentRequestButtons()
{
	stripe_isPaymentRequestInit = true;
	var paymentRequest = stripe_request_api.paymentRequest({
		country: stripeLanguageIso.toUpperCase(),
		currency: currency_stripe.toLowerCase(),
		total: {
			label: 'Amount',
			amount: amount_ttl,
		},
	});

	var elements = stripe_request_api.elements();
	var prButton = elements.create('paymentRequestButton', {
		paymentRequest,
		style: {
			paymentRequestButton: {
			  height: '44px',
			},
		},
    });

	// Check the availability of the Payment Request API first.
	paymentRequest.canMakePayment().then(function(result) {
		if (result) {
			document.getElementById('payment-request-button').style.display = 'block';
			prButton.mount('#payment-request-button');
		} else {
			document.getElementById('payment-request-button').style.display = 'none';
		}
	});

	paymentRequest.on('token', function(ev) {
		// console.log('[Payment Request Demo] Got token:', ev.token.id);
		// document.getElementById('payment-request-result').innerHTML = '<strong>Token created!</strong> <code>' + ev.token.id + '</code>';
		// ev.complete('success');

		// Send the token to your server to charge it!
		fetch(ajaxUrlStripe, {
			method: 'POST',
			body: JSON.stringify({stripeToken: ev.token.id, cardType: ev.token.card.brand, cardHolderName: ''}),
		}).then(function(response) {
			console.log(response)
			if (response.code) {
			  // Report to the browser that the payment was successful, prompting
			  // it to close the browser payment interface.
			  ev.complete('success');
			  location.replace(data.url);
			} else {
			  // Report to the browser that the payment failed, prompting it to
			  // re-show the payment interface, or show an error message and close
			  // the payment interface.
			  ev.complete('fail');
			}
		});
    });
}


// {
//   "id": "tok_1BswfiHumgdz3GrJA36UJ88m",
//   "object": "token",
//   "card": {
//     "id": "card_1BswfiHumgdz3GrJyOaWiXco",
//     "object": "card",
//     "address_city": "VILLE D AVRAY",
//     "address_country": "FR",
//     "address_line1": "7 avenue des cedres",
//     "address_line1_check": "unchecked",
//     "address_line2": "",
//     "address_state": "",
//     "address_zip": "92410",
//     "address_zip_check": "unchecked",
//     "brand": "Visa",
//     "country": "FR",
//     "cvc_check": "unchecked",
//     "dynamic_last4": null,
//     "exp_month": 11,
//     "exp_year": 2020,
//     "funding": "credit",
//     "last4": "4216",
//     "metadata": {},
//     "name": "valentin thibault",
//     "tokenization_method": null
//   },
//   "client_ip": "85.171.50.201",
//   "created": 1518024326,
//   "livemode": false,
//   "type": "card",
//   "used": false
// }