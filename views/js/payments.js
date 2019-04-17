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

$(function(){
  (async () => {
    'use strict';

    // Create references to the submit button.
    // const $submit = $('.stripe-submit-button');
    const $submit = $('#payment-confirmation button[type="submit"], .stripe-europe-payments[data-method="bancontact"], .stripe-europe-payments[data-method="ideal"], .stripe-europe-payments[data-method="giropay"], .stripe-europe-payments[data-method="sofort"], .stripe-submit-button');
    const $submitButtons = $('#payment-confirmation button[type="submit"], .stripe-submit-button');
    const submitInitialText = $submitButtons.text();

    let $form = '';
    let payment = '';
    let disableText = '';

    // Global variable to store the PaymentIntent object.
    let paymentIntent;

    /**
    * Setup Stripe Elements.
    */

    // Create a Stripe client.
    const stripe = Stripe(stripe_pk, { betas: ['payment_intent_beta_3'] });

    // Create an instance of Elements and prepare the CSS
    const elements = stripe.elements();
    const style = JSON.parse(stripe_css);

    // Create a Card Element and pass some custom styles to it.
    let card;
    if ($("#stripe-card-element").length) {
      card = elements.create('card', { style });
      card.mount('#stripe-card-element');

      // Monitor change events on the Card Element to display any errors.
      card.on('change', ({error}) => {
        updateError($submitButtons, error);
      });

      // Create the payment request (browser based payment button).
      const paymentRequest = stripe.paymentRequest({
        country: stripe_merchant_country_code,
        currency: stripe_currency,
        total: { label: 'Total', amount: stripe_amount },
        requestPayerEmail: true
      });

      if ($('#stripe-payment-request-button').length > 0) {
        // Callback when a source is created.
        paymentRequest.on('source', async event => {
          // Confirm the PaymentIntent with the source returned from the payment request.
          const { error } = await stripe.confirmPaymentIntent(
            stripe_client_secret, { source: event.source.id, use_stripe_sdk: true }
          );

          if (error) {
            // Report to the browser that the payment failed.
            event.complete('fail');
            updateError($submitButtons, {error});
          } else {
            // Report to the browser that the confirmation was successful, prompting
            // it to close the browser payment method collection interface.
            event.complete('success');
            // Let Stripe.js handle the rest of the payment flow, including 3D Secure if needed.
            const response = await stripe.handleCardPayment(stripe_client_secret);
            handlePayment(response);
          }
        });

        // Create the Payment Request Button.
        const prButton = elements.create('paymentRequestButton', { paymentRequest });

        // Check if the Payment Request is available.
        if (await paymentRequest.canMakePayment()) {
          prButton.mount('#stripe-payment-request-button');
          // TODO: show additional instructions
        }
      }
    }

    // Create a IBAN Element and pass the right options for styles and supported countries.
    let iban;
    if ($("#stripe-iban-element").length) {
      iban = elements.create('iban', { style, supportedCountries: ['SEPA'] });
      iban.mount('#stripe-iban-element');

      // Monitor change events on the IBAN Element to display any errors.
      iban.on('change', ({error, bankName}) => {
        updateError($submitButtons, error);
      });
    }

    // Create a iDEAL Bank Element and pass the style options, along with an extra `padding` property.
    let idealBank;
    if ($("#stripe-ideal-bank-element").length) {
      idealBank = elements.create('idealBank', {
        style: {base: Object.assign({padding: '10px 15px'}, style.base)},
      });

      // Mount the iDEAL Bank Element on the page.
      idealBank.mount('#stripe-ideal-bank-element');
    }

    /**
    * Handle the form submission.
    *
    * This uses Stripe.js to confirm the PaymentIntent using payment details collected
    * with Elements.
    *
    * Please note this form is not submitted when the user chooses the "Pay" button
    * or Apple Pay, Google Pay, and Microsoft Pay since they provide name and
    * shipping information directly.
    */
    $submit.click(async event => {
      if (!$('.stripe-payment-form:visible').length) {
        return true;
      }
      event.preventDefault();

      // Retrieve the payment method.
      if ($('#payment-confirmation button[type="submit"]').length > 0) {
        /* Prestashop 1.7 */
        $form = $('.stripe-payment-form:visible');
        payment = $('input[name="stripe-payment-method"]', $form).val();
        disableText = event.currentTarget;
      } else {
        /* Prestashop 1.6 */
        $form = event.currentTarget;
        payment = event.currentTarget.dataset.method;
        disableText = event.currentTarget;
      }

      // Disable the Pay button to prevent multiple click events.
      disableSubmit(disableText, 'Processing…');

      if (payment === 'card') {
        // Let Stripe.js handle the confirmation of the PaymentIntent with the card Element.
        const response = await stripe.handleCardPayment(
          stripe_client_secret, card, { source_data: { owner: { name: stripe_fullname } } }
        );
        handlePayment(response);
      } else if (payment === 'sepa_debit') {
        // Confirm the PaymentIntent with the IBAN Element and additional SEPA Debit source data.
        const response = await stripe.confirmPaymentIntent(
          stripe_client_secret, iban, {
            source_data: {
              type: 'sepa_debit', owner: { name: stripe_fullname, email: stripe_email },
              mandate: { notification_method: 'email' }
            }
          }
        );
        handlePayment(response);
      } else {
        // Prepare all the Stripe source common data.
        const sourceData = {
          type: payment, amount: stripe_amount, currency: stripe_currency,
          owner: { name: stripe_fullname, email: stripe_email },
          redirect: { return_url: stripe_validation_return_url },
          metadata: { paymentIntent: stripe_payment_id }
        };

        // Add extra source information which are specific to a payment method.
        switch (payment) {
          case 'ideal':
            // iDEAL: Add the selected Bank from the iDEAL Bank Element.
            const {source} = await stripe.createSource(idealBank, sourceData);
            handleSourceActivation(source, $form);
            return;
            break;
          case 'sofort':
            // SOFORT: The country is required before redirecting to the bank.
            sourceData.sofort = { country: stripe_address_country_code };
            break;
        }

        // Create a Stripe source with the common data and extra information.
        // console.log(sourceData);
        const {source} = await stripe.createSource(sourceData);
        handleSourceActivation(source, $form);
      }

      event.stopPropagation();

      return false;
    });

    // Handle new PaymentIntent result
    function handlePayment(response) {
      if (response.error) {
        updateError($submitButtons, response.error);
      } else {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: stripe_validation_return_url,
            data: {
                response: response,
            },
            success: function(datas) {
                if (datas['code'] == 1) {
                  window.location.replace(datas['url']);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
      }
    }

    // Handle activation of payment sources not yet supported by PaymentIntents
    function handleSourceActivation(source, element) {
      switch (source.flow) {
        case 'none':
          // Sources with flow as none don't require any additional action
          if (source.type === 'wechat') {
            // Display the QR code.
            const qrCode = new QRCode('stripe-wechat-qrcode', {
              text: source.wechat.qr_code_url, width: 128, height: 128,
              colorDark: '#424770', colorLight: '#f8fbfd', correctLevel: QRCode.CorrectLevel.H,
            });

            // Hide the previous text and update the call to action.
            $(".stripe-wechat-before").hide();
            $(".stripe-wechat-after").show();

            // Start polling the PaymentIntent status.
            pollPaymentIntentStatus();
          }
          break;
        case 'redirect':
          disableSubmit(disableText, 'Redirecting…');
          window.location.replace(source.redirect.url);
          break;
        case 'receiver':
          // Display the receiver address to send the funds to.
          // TODO: move this to order confirmation
          checkoutElement.classList.add('success', 'receiver');
          const receiverInfo = confirmationElement.querySelector(
            '.receiver .info'
          );
          let amount = store.formatPrice(source.amount, stripe_currency);
          switch (source.type) {
            case 'multibanco':
            // Display the Multibanco payment information to the user.
            const multibanco = source.multibanco;
            receiverInfo.innerHTML = `
            <ul>
            <li>Amount (Montante): <strong>${amount}</strong></li>
            <li>Entity (Entidade): <strong>${multibanco.entity}</strong></li>
            <li>Reference (Referencia): <strong>${multibanco.reference}</strong></li>
            </ul>`;
            break;
            default:
            console.log('Unhandled receiver flow.', source);
          }
          // Poll the PaymentIntent status.
          pollPaymentIntentStatus();
          break;
        default:
        // Customer's PaymentIntent is received, pending payment confirmation.
        break;
      }
    }

    async function pollPaymentIntentStatus() {
      const endStates = ['succeeded', 'processing', 'canceled'];
      // Retrieve the PaymentIntent status from our server.
      const response = await stripe.retrievePaymentIntent(stripe_client_secret);
      console.log(response.paymentIntent.status);
      if (!endStates.includes(response.paymentIntent.status)) {
        setTimeout(pollPaymentIntentStatus, 1000); // Every second
      } else {
        handlePayment(response);
      }
    };

    // Update error message
    function updateError(element, error) {
      const $error = $(".stripe-payment-form:visible .stripe-error-message");
      var elementError = $(element).siblings('.stripe-error-message');
      var disableElement = $(element).siblings('.stripe-submit-button');
      if (error) {
        if (prestashop_version == '1.6') {
          $(elementError).text(error.message).show();
        } else {
          $error.text(error.message).show();
        }
        enableSubmit($submitButtons);
      } else {
        if (prestashop_version == '1.6') {
          $(elementError).text("").hide();
        } else {
          $error.text("").hide();
        }
        enableSubmit($submitButtons);
      }
    }

    function disableSubmit(element, text) {
      $(element).prop('disabled', true);
      $(element).text(text ? text : submitInitialText);
    }

    function enableSubmit(element) {
      $(element).prop('disabled', false);
      $(element).text(submitInitialText);
    }
  })();
})