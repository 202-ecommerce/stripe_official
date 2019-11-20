/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) Stripe
 * @license   Commercial license
 */

$(function(){
  let $form = '';
  const initStripe = async () => {
    'use strict';

    // Create references to the submit button.
    const $submit = $('#payment-confirmation button[type="submit"], .stripe-europe-payments[data-method="bancontact"], .ideal-submit-button[data-method="ideal"], .stripe-europe-payments[data-method="giropay"], .stripe-europe-payments[data-method="sofort"], .stripe-submit-button');
    const $submitButtons = $('#payment-confirmation button[type="submit"], .stripe-submit-button');
    const submitInitialText = $submitButtons.text();

    $form = $('#stripe-card-payment');
    let payment = '';
    let disableText = '';

    // Global variable to store the PaymentIntent object.
    let paymentIntent;

    let cardType;

    // Get Stripe amount. On PS1.6 with OPC, the checkout page isn't refreshed
    // when updating cart quantity / carrier, so we need to update our data.
    if ($('#stripe-amount').length) {
        stripe_amount = parseInt($('#stripe-amount').val());
    }

    // Disabled card form (enter button)
    $form.on('submit', (event) => {
      event.preventDefault();
    });

    /**
    * Setup Stripe Elements.
    */
    // Create a Stripe client.
    const stripe = Stripe(stripe_pk, { betas: ['payment_intent_beta_3'] });

    // Create an instance of Elements and prepare the CSS
    const elements = stripe.elements({
      locale: stripe_locale
    });
    const style = JSON.parse(stripe_css);

    // Create a Card Element and pass some custom styles to it.
    let card;
    let cardExpiry;
    let cardCvc;
    let cardPostalCode;
    if ($("#stripe-card-element").length || $("#stripe-card-number").length) {
      if (stripe_reinsurance_enabled == 'on') {
        card = elements.create('cardNumber');
        card.mount('#stripe-card-number');
        cardExpiry = elements.create('cardExpiry');
        cardExpiry.mount('#stripe-card-expiry');
        cardCvc = elements.create('cardCvc');
        cardCvc.mount('#stripe-card-cvc');
        if (stripe_postcode_disabled != 'on') {
          cardPostalCode = elements.create('postalCode');
          cardPostalCode.mount('#stripe-card-postalcode');
        }
      } else {
        if (stripe_postcode_disabled == 'on') {
          card = elements.create('card', { style, hidePostalCode: true });
        } else {
          card = elements.create('card', { style });
        }
        card.mount('#stripe-card-element');
      }


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
          } else {
            // Report to the browser that the confirmation was successful, prompting
            // it to close the browser payment method collection interface.
            event.complete('success');
            $submit.attr('disabled', 'disabled');
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
          $('.card-payment-informations').show();
        }

        prButton.on('click', function(event) {
          if (prestashop_version == '1.7') {
            if ($submit.attr('disabled') == "disabled") {
              $('.stripe-payment-request-button-warning').modal('show');
              event.preventDefault();
            }
          }
        });
      }

      card.addEventListener('change', function(event) {
          setOutcome(event);
          cardType = event.brand;
          if (typeof cardType != "undefined" && cardType != "unknown") {
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
              card_logo.src = stripe_module_dir + '/views/img/cc-' + cardType.toLowerCase() +'.png';
              card_logo.id = "img-"+cardType;
              card_logo.className = "img-card";
              $('#stripe-card-number').append($(card_logo));
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

      if (stripe_reinsurance_enabled == 'on') {
        cardExpiry.addEventListener('change', function(event) {
            setOutcome(event);
        });

        cardCvc.addEventListener('change', function(event) {
            setOutcome(event);
        });

        if (stripe_postcode_disabled != 'on') {
          cardPostalCode.addEventListener('change', function(event) {
              setOutcome(event);
          });
        }
      }
    }

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
      if (prestashop_version == '1.7') {
        /* Prestashop 1.7 */
        $form = $('.stripe-payment-form:visible');
        payment = $('input[name="stripe-payment-method"]', $form).val();
        disableText = event.currentTarget;
      } else {
        /* Prestashop 1.6 */
        $form = event.currentTarget;
        payment = event.currentTarget.dataset.method;
        disableText = event.currentTarget;

        if (typeof stripe_compliance != 'undefined' && $('#uniform-cgv').find('input#cgv').prop("checked") !== true) {
          var error = { "message" : 'Please accept the CGV' };
          updateError($submitButtons, error);
          return false;
        }
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
        enableSubmit($submitButtons);
      } else {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            async: false,
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
          enableSubmit($submitButtons);
        } else {
          $error.text(error.message).show();
        }
      } else {
        if (prestashop_version == '1.6') {
          $(elementError).text("").hide();
        } else {
          $error.text("").hide();
        }
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
  };

  initStripe();

  /* for Prestashop 1.6 if eu_compliance module is used */
  $('.payment_module.pointer-box').click(function(event) {
    if ($(this).parent().find('.stripe-payment-form').length > 0) {
      $(this).parent().find('.payment_option_form').show();
    } else {
      $('.stripe-payment-form').parent().hide();
    }
  });
  /* END for Prestashop 1.6 if eu_compliance module is used */

  const observer = new MutationObserver((mutations) => {
    $.each(mutations, function(i, mutation) {
      const addedNodes = $(mutation.addedNodes);
      const selector = '#stripe-card-payment';
      const filteredEls = addedNodes.find(selector).addBack(selector);
      if (filteredEls.length) {
        initStripe();
      }
    })
  });

  observer.observe(document.body, {childList: true, subtree: true});
})
