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
	   <span class="stripe-module-intro">{l s='Beautiful, smart payment forms to improve your conversion rate on your PrestaShop website' mod='prestastripe'}</span>
	</div>
	<div class="stripe-module-wrap">
		 <div class="stripe-module-col1 floatRight"></div>
		 <div class="stripe-module-col2">
		 	<div class="stripe-module-col1inner">
			  	- <span><a href="https://stripe.com/signup" rel="external" target="_blank">{l s='Create your Stripe account in 10 minutes ' mod='prestastripe'}</a></span>
				{l s=' and immediately start accepting payments via Visa, MasterCard and American Express (no additional
				   contract/merchant ID needed from your bank)' mod='prestastripe'}<br>
				- <span>{l s='Improve your conversion rate  ' mod='prestastripe'}</span>
				{l s=' by offering a seamless payment experience to your customers: Stripe lets you host the payment form on your
				own pages, without redirection to a bank third-part page.' mod='prestastripe'}<br>
				- <span>{l s='Keep you fraud under control ' mod='prestastripe'}</span>
				{l s='thanks to customizable 3D-Secure and' mod='prestastripe'}
				<a target="_blank" href="https://stripe.com/radar">{l s='Stripe Radar' mod='prestastripe'}</a>
				{l s=', our suite of anti-fraud tools.' mod='prestastripe'}<br>
				- <span>{l s='Easily refund ' mod='prestastripe'}</span>
				{l s='your orders through your PrestaShop’s back-office (and automatically update your PrestaShop order status).' mod='prestastripe'}<br>
				- {l s='Start selling abroad by offering payments in ' mod='prestastripe'}
				<span>{l s='135+ currencies.' mod='prestastripe'}</span><br><br>
				<p>{l s='Find out more about Stripe on our website: ' mod='prestastripe'}
				<a target="_blank" href="https://stripe.com/fr">www.stripe.com</a></p>
				<br>
				<p><span>{l s='How much does Stripe cost?' mod='prestastripe'}</span></p><br>
				<p>
					{l s='For European companies, Stripe charges (per successful transaction):' mod='prestastripe'}<br>
					{l s='- 1.4% + €0.25/£0.20 with a European card' mod='prestastripe'}<br>
					{l s='- 2.9% + €0.25/£0.20 with a non-European card' mod='prestastripe'}<br>
					{l s='Stripe has no setup fees, no monthly fees, no validation fees, no refund fees, and no card storage fees. ' mod='prestastripe'}<br>
					{l s='There’s no additional fee for failed charges or refunds.' mod='prestastripe'}
				</p><br>
				<p>{l s='If you’d like to learn more about our simple pricing, please check our website: ' mod='prestastripe'}
				<a target="_blank" href="https://www.stripe.com/pricing">www.stripe.com/pricing</a></p><br>
				<p>{l s='We offer customized pricing for larger businesses. If you accept more than €30,000 per month,' mod='prestastripe'}
					<a target="_blank" href="https://stripe.com/contact/sales"> {l s='get in touch' mod='prestastripe'}</a></p>
			</div>


			<!--<div class="stripe-module-col2inner">
				<h3>{l s='Accept payments worldwide using all major credit cards' mod='prestastripe'}</h3>
				<p><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/stripe-cc.png" alt="stripe" class="stripe-cc" /><a href="https://stripe.com/signup" class="stripe-module-btn" target="_blank">
				<strong>{l s='Create a FREE Account!' mod='prestastripe'}</strong></a></p>
			</div>-->
		</div>
	</div>
</div>
