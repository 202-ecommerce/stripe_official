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

$(function() {
	  $('.faq-item').click(
		    function(){
			      if($(this).find('.faq-content').is(':visible'))
			      {
				        $(this).find('.faq-content').slideUp('fast');
				        $(this).find('.expand').html('+');
			      }
			      else
			      {
				        $('.faq-content').hide('fast');
				        $(this).find('.faq-content').slideDown('fast');
				        $('.expand').html('+');
				        $(this).find('.expand').html('-');
			      }
		    }
	  );
	  $('.faq-item a').click(
		    function(e){
			      e.stopPropagation();
		    }
	  );
});
