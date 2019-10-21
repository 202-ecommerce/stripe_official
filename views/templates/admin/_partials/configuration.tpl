{*
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
*}

<form id="configuration_form" class="defaultForm form-horizontal stripe_official" action="#stripe_step_1" method="post" enctype="multipart/form-data" novalidate="">
	<input type="hidden" name="submit_login" value="1">
	<div class="panel" id="fieldset_0">
		<div class="form-wrapper">
			<div class="form-group stripe-connection">
				{assign var='stripe_url' value='https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]='}
				{{l s='[a @href1@]Create your Stripe account in 10 minutes[/a] and immediately start accepting payments via Visa, MasterCard and American Express (no additional contract/merchant ID needed from your bank).' mod='stripe_official'}|stripelreplace:['@href1@' => {{$stripe_url|cat:$return_url|escape:'htmlall':'UTF-8'}}, '@target@' => {'target="blank"'}]}<br>

				<div class="connect_btn">
					<a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]={$return_url|escape:'htmlall':'UTF-8'}" class="stripe-connect">
						<span>{l s='Connect with Stripe' mod='stripe_official'}</span>
					</a>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Mode' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="STRIPE_MODE" id="STRIPE_MODE_ON" value="1" {if $stripe_mode == 1}checked="checked"{/if}>
						<label for="STRIPE_MODE_ON">{l s='test' mod='stripe_official'}</label>
						<input type="radio" name="STRIPE_MODE" id="STRIPE_MODE_OFF" value="0" {if $stripe_mode == 0}checked="checked"{/if}>
						<label for="STRIPE_MODE_OFF">{l s='live' mod='stripe_official'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block"></p>
				</div>
				<span>{l s='Now that you have created your Stripe account, you have to enter below your API keys in both test and live mode.' mod='stripe_official'}</span>
				<br/>
				<span>
					{{l s='These API keys can be found and managed from your Stripe [a @href1@]dashboard[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/account/apikeys'}, '@target@' => {'target="blank"'}]}
				</span>
			</div>

			<div class="form-group" {if $stripe_mode == 1}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Stripe Publishable Key' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_PUBLISHABLE" id="public_key" value="{$stripe_publishable|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group" {if $stripe_mode == 1}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Stripe Secrey Key' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_KEY" id="secret_key" value="{$stripe_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"{if $stripe_mode == 0}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Stripe Test Publishable Key' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_TEST_PUBLISHABLE" id="test_public_key" value="{$stripe_test_publishable|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"{if $stripe_mode == 0}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Stripe Test Secrey Key' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_TEST_KEY" id="test_secret_key" value="{$stripe_test_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>

			<div id="conf-payment-methods">
				<p><b>{l s='Testing Stripe' mod='stripe_official'}</b></p>
				<ul>
					<li>{l s='Toggle the button above to Test Mode.' mod='stripe_official'}</li>
					<li>
						{{l s='To perform test payments, you can use test card numbers available in our [a @href1@]documentation[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'http://www.stripe.com/docs/testing'}, '@target@' => {'target="blank"'}]}
					</li>
					<li>{l s='In Test Mode, you can not run live charges.' mod='stripe_official'}</li>
				</ul>
				<p><b>{l s='Using Stripe Live' mod='stripe_official'}</b></p>
				<ul>
					<li>{l s='Toggle the button above to Live Mode.' mod='stripe_official'}</li>
					<li>{l s='In Live Mode, you can not run test charges.' mod='stripe_official'}</li>
				</ul>

				<p><b>{l s='Set up the form' mod='stripe_official'}</b></p>
				<ol item="1">
					<li>
						<p>{l s='Options for the card payment form' mod='stripe_official'}</p>

						<div class="form-group">
							<input type="checkbox" id="reinsurance" name="reinsurance" {if $reinsurance}checked="checked"{/if}/>
							<label for="reinsurance">{l s='Activate extended display containing reinsurance elements (logo of cards. You must choose to display the cards you configured on Stripe\'s dashboard)' mod='stripe_official'}</label><br/>

							<input type="checkbox" id="visa" name="visa" {if $visa}checked="checked"{/if}/>
							<label for="visa">{l s='Visa' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="mastercard" name="mastercard" {if $mastercard}checked="checked"{/if}/>
							<label for="mastercard">{l s='Mastercard' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="american_express" name="american_express" {if $american_express}checked="checked"{/if}/>
							<label for="american_express">{l s='American Express' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="cb" name="cb" {if $cb}checked="checked"{/if}/>
							<label for="cb">{l s='CB (Cartes Bancaires)' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="diners_club" name="diners_club" {if $diners_club}checked="checked"{/if}/>
							<label for="diners_club">{l s='Diners Club / Discover' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="union_pay" name="union_pay" {if $union_pay}checked="checked"{/if}/>
							<label for="union_pay">{l s='China UnionPay' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="jcb" name="jcb" {if $jcb}checked="checked"{/if}/>
							<label for="jcb">{l s='JCB' mod='stripe_official'}</label><br/>
							<input type="checkbox" id="discovers" name="discovers" {if $discovers}checked="checked"{/if}/>
							<label for="discovers">{l s='Discovers' mod='stripe_official'}</label><br/>
						</div>

						<div class="form-group">
							<input type="checkbox" id="postcode" name="postcode" {if $postcode}checked="checked"{/if}/>
							<label for="postcode">{l s='Disable the Postal Code field for cards from the United States, United Kingdom and Canada (not recommended *).' mod='stripe_official'}</label><br/>
							<span>*{l s='Collecting postal code optimizes the chances of successful payment for these countries.' mod='stripe_official'}</span>
						</div>

						<div class="form-group">
							<input type="checkbox" id="cardholdername" name="cardholdername" {if $cardholdername}checked="checked"{/if}/>
							<label for="cardholdername">{l s='Activate display of card holder name' mod='stripe_official'}</label>
						</div>
					</li>
					<li>
						<p>{l s='Additional payment methods (For users in Europe only): iDEAL, Bancontact, SOFORT and Giropay.' mod='stripe_official'}</p>
						<p>
							{{l s='These payment methods are available within this plugin for our European users only. To activate them, follow these [b]three steps:[/b]' mod='stripe_official'}|stripelreplace}
						</p>

						<ol type="A">
							<li>
								{l s='Select below each payment method you wish to offer on your website :' mod='stripe_official'}
								<br><br>
								<div class="form-group">
									<input type="checkbox" id="ideal" name="ideal" {if $ideal}checked="checked"{/if}/>
									<label for="ideal">{l s='Activate iDEAL (if you have Dutch customers)' mod='stripe_official'}</label><br>
									<input type="checkbox" id="bancontact" name="bancontact" {if $bancontact}checked="checked"{/if}/>
									<label for="bancontact">{l s='Activate Bancontact (if you have Belgian customers)' mod='stripe_official'}</label><br>
									<input type="checkbox" id="sofort" name="sofort" {if $sofort}checked="checked"{/if}/>
									<label for="sofort">{l s='Activate SOFORT (if you have German, Austrian customers)' mod='stripe_official'}</label><br>
									<input type="checkbox" id="giropay" name="giropay" {if $giropay}checked="checked"{/if}/>
									<label for="giropay">{l s='Activate Giropay (if you have German customers)' mod='stripe_official'}</label><br>
									<input type="checkbox" id="applepay_googlepay" name="applepay_googlepay" {if $applepay_googlepay}checked="checked"{/if}/>
									<label for="applepay_googlepay">
										{{l s='Enable Payment Request Buttons. (Apple Pay/Google Pay)[br]By using Apple Pay, you agree to [a @href1@]Stripe[/a] and [a @href2@]Apple[/a]\'s terms of service.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/us/legal'}, '@href2@' => {'https://www.apple.com/legal/internet-services/terms/site.html'}, '@target@' => {'target="blank"'}]}
									</label>
								</div>

							</li>
							<li>
								{l s='To track correctly charges performed with these payment methods, you’ll need to add a “webhook”. A webhook is a way to be notified when an event (such as a successful payment) happens on your website.' mod='stripe_official'}
								<br><br>
								<ul>
									<li>
										{{l s='Go on the webhook page of your Stripe dashboard: [a @href1@]https://dashboard.stripe.com/account/webhooks[/a]' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/account/webhooks'}, '@target@' => {'target="blank"'}]}
									</li>
									<li>{l s='Click on "Add Endpoint" and copy/paste this URL in the "URL to be called" field:' mod='stripe_official'} {$url_webhhoks|escape:'htmlall':'UTF-8'}</li>
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
							<li>
								{{l s='Activate these payment methods on your [a @href1@]Stripe dashboard[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/account/payments/settings'}, '@target@' => {'target="blank"'}]}
							</li>
							<p>{l s='After clicking "Activate", the payment method is shown as pending with an indication of how long it might take to activate.' mod='stripe_official'}
								{l s='Once you\'ve submitted this form, the payment method will move from pending to live within 10 minutes.' mod='stripe_official'}</p>
						</ol>
					</li>
				</ol>


			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" value="1" id="configuration_form_submit_btn" name="submit_login" class="btn btn-default pull-right button">
				<i class="process-icon-save"></i>
				{l s='Save' mod='stripe_official'}
			</button>
		</div>
	</div>
</form>