{*
* 2007-2018 PrestaShop
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
*	@copyright	2007-2018 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div class="stripe-module-wrapper">
	<div class="stripe-module-header">
	   <span class="stripe-module-intro">{l s='Improve your conversion rate and securely charge your customers with Stripe, the easiest payment platform' mod='stripe_official'}</span>
	</div>
	<div class="stripe-module-wrap">
		 <div class="stripe-module-col1 floatRight"></div>
		 <div class="stripe-module-col2">
		 	<div class="stripe-module-col1inner">
			 	- <span><a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]={$return_url}" rel="external" target="_blank">{l s='Create your Stripe account in 10 minutes' mod='stripe_official'}</a> </span>
				{l s='and immediately start accepting payments via Visa, MasterCard and American Express (no additional contract/merchant ID needed from your bank)' mod='stripe_official'}.<br>
				<div class="connect_btn">
					<a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]={$return_url}" class="stripe-connect">
						<span>{l s='Connect with Stripe' mod='stripe_official'}</span>
					</a>
				</div>
				- <span>{l s='Improve your conversion rate' mod='stripe_official'} </span>
				{l s='by offering a seamless payment experience to your customers: Stripe lets you host the payment form on your own pages, without redirection to a bank third-part page.' mod='stripe_official'}<br>
				- <span>{l s='Keep your fraud under control' mod='stripe_official'}</span> {l s='thanks to customizable 3D-Secure and' mod='stripe_official'}
				<a target="_blank" href="https://stripe.com/radar">{l s='Stripe Radar' mod='stripe_official'}</a>{l s=', our suite of anti-fraud tools.' mod='stripe_official'}<br>
				- <span>{l s='Easily refund ' mod='stripe_official'}</span>
				{l s='your orders through your PrestaShop’s back-office (and automatically update your PrestaShop order status).' mod='stripe_official'}<br>
				- {l s='Start selling abroad by offering payments in ' mod='stripe_official'}
				<span>{l s='135+ currencies' mod='stripe_official'}</span> {l s='and 4 local payment methods (iDEAL, Bancontact, SOFORT, Giropay).' mod='stripe_official'}<br><br>
				<img src="{$module_dir}/views/img/started.png" style="width:100%;">
				<br><br>
				<p>{l s='Find out more about Stripe on our website: ' mod='stripe_official'}
				<a target="_blank" href="https://stripe.com/fr">www.stripe.com</a></p>
				<br>
				<p><span>{l s='How much does Stripe cost?' mod='stripe_official'}</span></p>
				<p>
					{l s='Stripe has not setup fees, no monthly fees and no storage fees.' mod='stripe_official'}<br>
					{l s='There’s no additional fee for failed charges.' mod='stripe_official'}<br><br>
					{l s='For European companies, Stripe charges (per successful transaction) :' mod='stripe_official'}<br>
					- {l s='1.4% + €0.25/£0.20 with a European card' mod='stripe_official'}<br>
					- {l s='2.9% + €0.25/£0.20 with a non-European card' mod='stripe_official'}<br>
					{l s='For other payment methods, non-European merchants pricing and additional information, please check our website:' mod='stripe_official'} <a target="_blank" href="https://www.stripe.com/pricing">www.stripe.com/pricing</a>.</p><br>
				<p>{l s='We offer customized pricing for larger businesses. If you accept more than €30,000 per month,' mod='stripe_official'}
					<a target="_blank" href="https://stripe.com/contact/sales"> {l s='get in touch' mod='stripe_official'}</a>.</p>
				<div class="connect_btn">
					<a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]={$return_url}" class="stripe-connect">
						<span>{l s='Connect with Stripe' mod='stripe_official'}</span>
					</a>
				</div>
			</div>
			<!--<div class="stripe-module-col2inner">
				<h3>{l s='Accept payments worldwide using all major credit cards' mod='stripe_official'}</h3>
				<p><img src="{$module_dir}/views/img/stripe-cc.png" alt="stripe" class="stripe-cc" /><a href="https://stripe.com/signup" class="stripe-module-btn" target="_blank">
				<strong>{l s='Create a FREE Account!' mod='stripe_official'}</strong></a></p>
			</div>-->
		</div>
	</div>
</div>
