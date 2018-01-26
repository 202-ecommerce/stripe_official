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

<div id="conf-payment-methods">
	<p><b>{l s='Testing Stripe' mod='stripe_official'}</b></p>
	<ul>
		<li>{l s='Toggle the button above to Test Mode.' mod='stripe_official'}</li>
		<li>{l s='To perform test payments, you can use test card numbers available in our' mod='stripe_official'}
		<a target="_blank" href="http://www.stripe.com/docs/testing">{l s='documentation.' mod='stripe_official'}</a></li>
		<li>{l s='In Test Mode, you can not run live charges.' mod='stripe_official'}</li>
	</ul>
	<p><b>{l s='Using Stripe Live' mod='stripe_official'}</b></p>
	<ul>
		<li>{l s='Toggle the button above to Live Mode.' mod='stripe_official'}</li>
		<li>{l s='In Live Mode, you can not run test charges.' mod='stripe_official'}</li>
	</ul>

	<p><b>{l s='Additional payment methods (For users in Europe only): iDEAL, Bancontact, SOFORT and Giropay.' mod='stripe_official'}</b></p>
	<p>{l s='These payment methods are available within this plugin for our European users only. To activate them, follow these' mod='stripe_official'}
	<b> {l s='three steps:' mod='stripe_official'}</b></p>
	<ol item="1">
		<li>
			{l s='Select below each payment method you wish to offer on your website :' mod='stripe_official'}
			<br><br>
			<div class="form-group">
				<input type="checkbox" id="ideal" name="ideal" {if $ideal}checked="checked"{/if}/>
				<label>{l s='Activate iDEAL (if you have Dutch customers)' mod='stripe_official'}</label><br>
				<input type="checkbox" id="bancontact" name="bancontact" {if $bancontact}checked="checked"{/if}/>
				<label>{l s='Activate Bancontact (if you have Belgian customers)' mod='stripe_official'}</label><br>
				<input type="checkbox" id="sofort" name="sofort" {if $sofort}checked="checked"{/if}/>
				<label>{l s='Activate SOFORT (if you have German, Austrian or Swiss customers)' mod='stripe_official'}</label><br>
				<input type="checkbox" id="giropay" name="giropay" {if $giropay}checked="checked"{/if}/>
				<label>{l s='Activate Giropay (if you have German, Austrian or Swiss customers)' mod='stripe_official'}</label>
			</div>

		</li>
		<li>
			{l s='To track correctly charges performed with these payment methods, you’ll need to add a “webhook”. A webhook is a way to be notified when an event (such as a successful payment) happens on your website.' mod='stripe_official'}
			<br><br>
			<ul>
				<li>{l s='Go on the webhook page of your Stripe dashboard:' mod='stripe_official'}
					<a target="_blank" href="https://dashboard.stripe.com/account/webhooks">https://dashboard.stripe.com/account/webhooks</a>
				</li>
				<li>{l s='Click on "Add Endpoint" and copy/paste this URL in the "URL to be called" field:' mod='stripe_official'} {$url_webhhoks}</li>
				<li>{l s='Set the "Events to send" radion button to "Live events"' mod='stripe_official'}</li>
				<li>{l s='Set the "Filter event" radio button to "Send all event types"' mod='stripe_official'}</li>
				<li>{l s='Click on "Add endpoint"' mod='stripe_official'}<br>
					<img class="img-example1" src="/modules/stripe_official//views/img/example1.png">
				</li>
				<li>{l s='Ultimately, your webhook dashboard page should look like this:' mod='stripe_official'}<br>
					<img class="img-example2" src="/modules/stripe_official//views/img/example2.png">
				</li>
			</ul>
		</li>
		<br>
		<li>{l s='Activate these payment methods on your' mod='stripe_official'}
			<a target="_blank" href="https://dashboard.stripe.com/account/payments/settings">{l s='Stripe dashboard.' mod='stripe_official'}</a>
		</li>
		<p>{l s='After clicking "Activate", the payment method is shown as pending with an indication of how long it might take to activate.' mod='stripe_official'}
			{l s='Once you\'ve submitted this form, the payment method will move from pending to live within 10 minutes.' mod='stripe_official'}</p>
	</ol>
</div>