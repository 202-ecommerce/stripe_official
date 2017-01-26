/**
* 2007-2016 PrestaShop
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

$(document).ready(function() {

    /* Css Radio Connection button 1.5 */
    $('#active_on').next().css('width', '25px');
    $('#active_off').next().css('width', '20px');
    $('#active_on').next().css('float', 'none');
    $('#active_off').next().css('float', 'none');

    /* Css Radio Refund 1.5 */
    $('#active_on_refund').next().css('width', '45px');
    $('#active_off_refund').next().css('width', '45px');
    $('#active_on_refund').next().css('float', 'none');
    $('#active_off_refund').next().css('float', 'none');


    /* Switch Option */
     var value = 0;
    value = $('input[name=_PS_STRIPE_mode]:checked').val();

    if (value == 1)
    {
        $("#secret_key").parent().prev().hide();
        $("#secret_key").parent().hide();
        $("#public_key").parent().prev().hide();
        $("#public_key").parent().hide();
        $("#test_secret_key").parent().prev().show();
        $("#test_secret_key").parent().show();
        $("#test_public_key").parent().prev().show();
        $("#test_public_key").parent().show();
    }
    else
    {
        $("#secret_key").parent().prev().show();
        $("#secret_key").parent().show();
        $("#public_key").parent().prev().show();
        $("#public_key").parent().show();
        $("#test_secret_key").parent().prev().hide();
        $("#test_secret_key").parent().hide();
        $("#test_public_key").parent().prev().hide();
        $("#test_public_key").parent().hide();
    }

    $('input[name=_PS_STRIPE_mode]').on('change', function() {
        value = $('input[name=_PS_STRIPE_mode]:checked').val();

        if (value == 1)
        {
            $("#secret_key").parent().prev().hide();
            $("#secret_key").parent().hide();
            $("#public_key").parent().prev().hide();
            $("#public_key").parent().hide();
            $("#test_secret_key").parent().prev().show();
            $("#test_secret_key").parent().show();
            $("#test_public_key").parent().prev().show();
            $("#test_public_key").parent().show();
        }
        else
        {
            $("#secret_key").parent().prev().show();
            $("#secret_key").parent().show();
            $("#public_key").parent().prev().show();
            $("#public_key").parent().show();
            $("#test_secret_key").parent().prev().hide();
            $("#test_secret_key").parent().hide();
            $("#test_public_key").parent().prev().hide();
            $("#test_public_key").parent().hide();
        }
    });

    var value = 0;
    value = $('input[name=_PS_STRIPE_refund_mode]:checked').val();

    if (value == 1)
    {
        $('#refund_amount').parent().prev().hide();
        $('#refund_amount').parent().hide();
    }
    else
    {
        $('#refund_amount').parent().prev().show();
        $('#refund_amount').parent().show();
    }

    $('input[name=_PS_STRIPE_refund_mode]').on('change', function() {
        value = $('input[name=_PS_STRIPE_refund_mode]:checked').val();
        if (value == 1)
        {
            $('#refund_amount').parent().prev().hide();
            $('#refund_amount').parent().hide();
        }
        else
        {
            $('#refund_amount').parent().prev().show();
            $('#refund_amount').parent().show();
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

    $('#configuration_form_2 input').on('change', function() {
        value = $('input[name=_PS_STRIPE_refund_mode]:checked').val();

        if (value == 1)
            $("#refund_amount").parent().parent().hide();
        else
            $("#refund_amount").parent().parent().show();
    });

    $('.process-icon-refresh').click(function(){
        $.ajax({
            url: validate + 'refresh.php',
        }).done(function(response) {
            $('.table').html(response);
        });
    });

});
