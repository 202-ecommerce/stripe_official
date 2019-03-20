/**
* 2007-2019 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author   PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA
* @license  http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/

(async () => {
  'use strict';

  // Create references to the submit button.
  const submitButton = document.getElementById('payment-confirmation').querySelector('button[type=submit]');

  // Global variable to store the PaymentIntent object.
  let paymentIntent;

  /**
  * Setup Stripe Elements.
  */

  // Create a Stripe client.
  const stripe = Stripe(stripe_pk, {
    betas: ['payment_intent_beta_3'],
  });

  // Create an instance of Elements.
  const elements = stripe.elements();

  // Prepare the styles for Elements.
  const style = JSON.parse(stripe_css);

  // Create a Card Element and pass some custom styles to it.
  let card;
  if ($("#stripe-card-element").length) {
    card = elements.create('card', { style });
    card.mount('#stripe-card-element');

    // Monitor change events on the Card Element to display any errors.
    card.on('change', ({error}) => {
      updateError(error);

      // Re-enable the Pay button.
      submitButton.disabled = false;
    });
  }

  // Create a IBAN Element and pass the right options for styles and supported countries.
  let iban;
  if ($("#stripe-iban-element").length) {
    iban = elements.create('iban', { style, supportedCountries: ['SEPA'] });
    iban.mount('#stripe-iban-element');

    // Monitor change events on the IBAN Element to display any errors.
    iban.on('change', ({error, bankName}) => {
      updateError(error);

      // Re-enable the Pay button.
      submitButton.disabled = false;
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

  // Create the payment request (browser based payment button).
  const paymentRequest = stripe.paymentRequest({
    country: stripe_merchant_country_code,
    currency: stripe_currency,
    total: { label: 'Total', amount: stripe_amount },
    requestPayerEmail: true
  });

  // Callback when a source is created.
  paymentRequest.on('source', async event => {
    // Confirm the PaymentIntent with the source returned from the payment request.
    const {error} = await stripe.confirmPaymentIntent(
      stripe_client_secret, { source: event.source.id, use_stripe_sdk: true }
    );

    if (error) {
      // Report to the browser that the payment failed.
      event.complete('fail');
      handlePayment({error});
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
  const paymentRequestButton = elements.create('paymentRequestButton', {
    paymentRequest
  });

  // Check if the Payment Request is available (or Apple Pay on the Web).
  const paymentRequestSupport = await paymentRequest.canMakePayment();

  if (paymentRequestSupport) {
    // Display the Pay button by mounting the Element in the DOM.
    paymentRequestButton.mount('#stripe-payment-request-button');
    // Show the payment request section.
    document.getElementById('payment-request').classList.add('visible');
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
  $('#payment-confirmation > .ps-shown-by-js > button').click(async event => {
    if (!$('.stripe-payment-form:visible').length) {
      return;
    }

    // Retrieve the payment method.
    const form = $('.stripe-payment-form:visible');
    const payment = $('input[name="stripe-payment-method"]', form).val();

    // Disable the Pay button to prevent multiple click events.
    submitButton.disabled = true;
    submitButton.textContent = 'Processing…';

    if (payment === 'card') {
      // Let Stripe.js handle the confirmation of the PaymentIntent with the card Element.
      const response = await stripe.handleCardPayment(
        stripe_client_secret, card, { source_data: { owner: { name } } }
      );
      handlePayment(response);
    } else if (payment === 'sepa_debit') {
      // Confirm the PaymentIntent with the IBAN Element and additional SEPA Debit source data.
      const response = await stripe.confirmPaymentIntent(
        stripe_client_secret, iban,
        {
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
        type: payment,
        amount: stripe_amount,
        currency: stripe_currency,
        owner: { name: stripe_fullname, email: stripe_email },
        redirect: { return_url: window.location.href },
        statement_descriptor: 'Stripe Payments Demo',
        metadata: { paymentIntent: stripe_payment_id }
      };

      // Add extra source information which are specific to a payment method.
      switch (payment) {
        case 'ideal':
        // iDEAL: Add the selected Bank from the iDEAL Bank Element.
        const {source} = await stripe.createSource(idealBank, sourceData);
        handleSourceActivation(source);
        return;
        break;
        case 'sofort':
        // SOFORT: The country is required before redirecting to the bank.
        sourceData.sofort = { stripe_address_country_code };
        break;
      }

      // Create a Stripe source with the common data and extra information.
      console.log(sourceData);
      const {source} = await stripe.createSource(sourceData);
      handleSourceActivation(source);
    }

    event.preventDefault();
    event.stopPropagation();

    return false;
  });

  // Handle new PaymentIntent result
  const handlePayment = paymentResponse => {
    const {paymentIntent, error} = paymentResponse;

    const checkoutElement = document.getElementById('checkout');
    const confirmationElement = document.getElementById('confirmation');

    if (error) {
      updateError(error);
    } else if (paymentIntent.status === 'succeeded') {
      // Success! Payment is confirmed. Update the interface to display the confirmation screen.
      checkoutElement.classList.remove('processing');
      checkoutElement.classList.remove('receiver');
      // Update the note about receipt and shipping (the payment has been fully confirmed by the bank).
      confirmationElement.querySelector('.note').innerText =
      'We just sent your receipt to your email address, and your items will be on their way shortly.';
      checkoutElement.classList.add('success');
    } else if (paymentIntent.status === 'processing') {
      // Success! Now waiting for payment confirmation. Update the interface to display the confirmation screen.
      checkoutElement.classList.remove('processing');
      // Update the note about receipt and shipping (the payment is not yet confirmed by the bank).
      confirmationElement.querySelector('.note').innerText =
      'We’ll send your receipt and ship your items as soon as your payment is confirmed.';
      checkoutElement.classList.add('success');
    } else {
      // Payment has failed.
      checkoutElement.classList.remove('success');
      checkoutElement.classList.remove('processing');
      checkoutElement.classList.remove('receiver');
      checkoutElement.classList.add('error');
    }
  };

  // Handle activation of payment sources not yet supported by PaymentIntents
  const handleSourceActivation = source => {
    const checkoutElement = document.getElementById('checkout');
    const confirmationElement = document.getElementById('confirmation');
    switch (source.flow) {
      case 'none':
      // Normally, sources with a `flow` value of `none` are chargeable right away,
      // but there are exceptions, for instance for WeChat QR codes just below.
      if (source.type === 'wechat') {
        // Display the QR code.
        const qrCode = new QRCode('wechat-qrcode', {
          text: source.wechat.qr_code_url,
          width: 128,
          height: 128,
          colorDark: '#424770',
          colorLight: '#f8fbfd',
          correctLevel: QRCode.CorrectLevel.H,
        });
        // Hide the previous text and update the call to action.
        form.querySelector('.payment-info.wechat p').style.display = 'none';
        let amount = store.formatPrice(
          stripe_amount,
          stripe_currency
        );
        submitButton.textContent = `Scan this QR code on WeChat to pay ${amount}`;
        // Start polling the PaymentIntent status.
        pollPaymentIntentStatus(stripe_payment_id, 300000);
      } else {
        console.log('Unhandled none flow.', source);
      }
      break;
      case 'redirect':
      // Immediately redirect the customer.
      submitButton.textContent = 'Redirecting…';
      window.location.replace(source.redirect.url);
      break;
      case 'code_verification':
      // Display a code verification input to verify the source.
      break;
      case 'receiver':
      // Display the receiver address to send the funds to.
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
      pollPaymentIntentStatus(stripe_payment_id);
      break;
      default:
      // Customer's PaymentIntent is received, pending payment confirmation.
      break;
    }
  };

  /**
  * Monitor the status of a source after a redirect flow.
  *
  * This means there is a `source` parameter in the URL, and an active PaymentIntent.
  * When this happens, we'll monitor the status of the PaymentIntent and present real-time
  * information to the user.
  */

  const pollPaymentIntentStatus = async (
    paymentIntent,
    timeout = 30000,
    interval = 500,
    start = null
  ) => {
    start = start ? start : Date.now();
    const endStates = ['succeeded', 'processing', 'canceled'];
    // Retrieve the PaymentIntent status from our server.
    const rawResponse = await fetch(`payment_intents/${paymentIntent}/status`);
    const response = await rawResponse.json();
    if (
      !endStates.includes(response.paymentIntent.status) &&
      Date.now() < start + timeout
    ) {
      // Not done yet. Let's wait and check again.
      setTimeout(
        pollPaymentIntentStatus,
        interval,
        paymentIntent,
        timeout,
        interval,
        start
      );
    } else {
      handlePayment(response);
      if (!endStates.includes(response.paymentIntent.status)) {
        // Status has not changed yet. Let's time out.
        console.warn(new Error('Polling timed out.'));
      }
    }
  };

  const url = new URL(window.location.href);
  const checkoutElement = document.getElementById('checkout');
  // Update the interface to display the processing screen.
  checkoutElement.classList.add('checkout', 'success', 'processing');

  // Update error message
  const updateError = (error) => {
    if (error) {
      $(".stripe-payment-form:visible .stripe-error-message").text(error.message).show();
    } else {
      $(".stripe-payment-form:visible .stripe-error-message").text("").hide();
    }
  };
})();
