{*
* 2007-2016 PrestaShop
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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2016 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div class="stripe-module-wrapper">
	<div class="stripe-module-header">
	   <span class="stripe-module-intro">{l s='Start accepting payments from anyone, anywhere.' mod='prestastripe'}</span>
	   	<a href="https://stripe.com/signup" rel="external" class="stripe-module-create-btn" target="_blank"><span>{l s='Create an Account' mod='prestastripe'}</span></a>
	</div>
	<div class="stripe-module-wrap">
		 <div class="stripe-module-col1 floatRight"></div>
		 <div class="stripe-module-col2">
		 	<div class="stripe-module-col1inner">
			  	   <h3>{l s='You\'ll love to use Stripe' mod='prestastripe'}</h3>
				   <p>{l s='With Stripe, you will have the ability to view full transaction details straight from your back office. No need to jump around to different platforms, the convenience of handling full and partial refunds are in your hands.' mod='prestastripe'}</p>
			</div>
			<div class="stripe-module-col1inner">
				<h3>{l s='Pricing like it should be' mod='prestastripe'}</h3>
				<p><strong>{l s='2.9% + 30 cents per successful charge. (for most countries)' mod='prestastripe'}</strong></p>
				<p>{l s='No setup fees, no monthly fees, no card storage fees, no hidden costs: you only get charged when you earn money.' mod='prestastripe'}</p>
				<p>{l s='For more information, get in contact with Stripe.' mod='prestastripe'}</p>
			</div>
			<div class="stripe-module-col1inner">
				<h3>{l s='Make a Refund' mod='prestastripe'}</h3>
				<p>{l s='Refunds allow you to refund a charge that has previously been created but not yet refunded.' mod='prestastripe'}</p>
				<p>{l s='Partial and full refund capabilities are available, any fees originally charged (shipping, handling, etc.) are also refundable.' mod='prestastripe'}</p>
			</div>
			<div class="stripe-module-col2inner">
				<h3>{l s='Accept payments worldwide using all major credit cards' mod='prestastripe'}</h3>
				<p><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/stripe-cc.png" alt="stripe" class="stripe-cc" /><a href="https://stripe.com/signup" class="stripe-module-btn" target="_blank">
				<strong>{l s='Create a FREE Account!' mod='prestastripe'}</strong></a></p>
			</div>
		</div>
	</div>
</div>
