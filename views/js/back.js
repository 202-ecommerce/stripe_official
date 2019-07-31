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

$(document).ready(function() {

	// multistore
	var old = $('.bootstrap.panel');
	$('#content').after(old);
	old.css('margin-left', '12%');


	var value = 0;
	value = $('input[name=STRIPE_MODE]:checked', '#configuration_form').val();

	if (value == 1)
	{
		$("#secret_key").parent().parent().hide();
		$("#public_key").parent().parent().hide();
		$("#test_secret_key").parent().parent().show();
		$("#test_public_key").parent().parent().show();
	}
	else
	{
		$("#secret_key").parent().parent().show();
		$("#public_key").parent().parent().show();
		$("#test_secret_key").parent().parent().hide();
		$("#test_public_key").parent().parent().hide();
	}

	$('#configuration_form input').on('change', function() {
		value = $('input[name=STRIPE_MODE]:checked', '#configuration_form').val();

		if (value == 1)
		{
			$("#secret_key").parent().parent().hide();
			$("#public_key").parent().parent().hide();
			$("#test_secret_key").parent().parent().show();
			$("#test_public_key").parent().parent().show();
		}
		else
		{
			$("#secret_key").parent().parent().show();
			$("#public_key").parent().parent().show();
			$("#test_secret_key").parent().parent().hide();
			$("#test_public_key").parent().parent().hide();
		}
	});

	/* Alert Confirmation Refund */
	$("#configuration_form_submit_btn_2").click(function(){
		if (confirm('Are you sure that you want to refund this order?'))
	  		return true;
		return false;
	});

	/* Refund Option */
	var value = 0;
	value = $('input[name=STRIPE_REFUND_MODE]:checked').val();

	if (value == 1)
		$(".partial-amount").hide();
	else
		$(".partial-amount").show();

	$('input[name=STRIPE_REFUND_MODE]').on('change', function() {
		value = $('input[name=STRIPE_REFUND_MODE]:checked').val();

		if (value == 1)
			$(".partial-amount").hide();
		else
			$(".partial-amount").show();
	});

	$('.process-icon-refresh').click(function(){
        $.ajax({
            url: transaction_refresh_url,
            data: {'token_stripe' : token_stripe,
            'id_employee' : id_employee}
        }).done(function(response) {
            $('.table-transaction').html(response);
        });
    });

	(function() {
		[].slice.call(document.querySelectorAll('.tabs')).forEach(function(el) {
			new PSTabs(el);
		});
	})();

    displayPayment();

    $('#applepay_googlepay').change(function(event) {
        displayPayment();
    });

    $('#product_payment').change(function(event) {
        if ($(this).is(':checked') === true) {
            $('#modal_applepay_googlepay').show();
        }
    });

    $('#modal_applepay_googlepay button[data-dismiss="modal"]').click(function(event) {
        $('#modal_applepay_googlepay').hide();
    });
});

function displayPayment(){
    if($('#applepay_googlepay').is(':checked') === true) {
        $('#display_product_payment').show();
    } else {
        $('#display_product_payment').hide();
    }
}