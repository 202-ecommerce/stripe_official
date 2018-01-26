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

	//rename switch fiels value labels
	$('label[for=_PS_STRIPE_mode_on]').html(stripe_test_mode);
	$('label[for=_PS_STRIPE_mode_off]').html(live);
	$('#section-shape-2 .form-group').first().append(conf_mode_description1+'<br>'+conf_mode_description2+' <a href="https://dashboard.stripe.com/account/apikeys" target="blank">'+conf_mode_description3+'</a>.');

	$('#section-shape-2 .panel .form-wrapper').append($('#conf-payment-methods'));

	// multistore
	var old = $('.bootstrap.panel');
	$('#content').after(old);
	old.css('margin-left', '12%');


	var value = 0;
	value = $('input[name=_PS_STRIPE_mode]:checked', '#configuration_form').val();

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
		value = $('input[name=_PS_STRIPE_mode]:checked', '#configuration_form').val();

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
	value = $('input[name=_PS_STRIPE_refund_mode]:checked').val();

	if (value == 1)
		$("#refund_amount").parent().parent().hide();
	else
		$("#refund_amount").parent().parent().show();

	$('input[name=_PS_STRIPE_refund_mode]').on('change', function() {
		value = $('input[name=_PS_STRIPE_refund_mode]:checked').val();

		if (value == 1)
			$("#refund_amount").parent().parent().hide();
		else
			$("#refund_amount").parent().parent().show();
	});

	$('.process-icon-refresh').click(function(){
        $.ajax({
            url: validate + 'refresh.php',
            data: {'token_stripe' : token_stripe,
            'id_employee' : id_employee}
        }).done(function(response) {
            $('.table').html(response);
        });
    });


});
