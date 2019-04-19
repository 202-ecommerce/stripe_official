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

<form id="configuration_form" class="defaultForm form-horizontal stripe_official" action="#stripe_step_2" method="post" enctype="multipart/form-data" novalidate="">
	<input type="hidden" name="submit_login" value="1">
	<div class="panel" id="fieldset_0">
		<div class="form-wrapper">
			<div class="form-group stripe-connection">
				<span><a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]={$return_url|escape:'htmlall':'UTF-8'}" rel="external" target="_blank">{l s='Create your Stripe account in 10 minutes' mod='stripe_official'}</a> </span>
				{l s='and immediately start accepting payments via Visa, MasterCard and American Express (no additional contract/merchant ID needed from your bank)' mod='stripe_official'}.<br>
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
				<span>{l s='These API keys can be found and managed from your Stripe' mod='stripe_official'} <a href="https://dashboard.stripe.com/account/apikeys"> {l s='dashboard' mod='stripe_official'}</a></span>
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
					<input type="text" name="STRIPE_KEY" id="secret_key" value="{$stripe_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
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
					<input type="text" name="STRIPE_TEST_KEY" id="test_secret_key" value="{$stripe_test_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>

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
							<label for="ideal">{l s='Activate iDEAL (if you have Dutch customers)' mod='stripe_official'}</label><br>
							<input type="checkbox" id="bancontact" name="bancontact" {if $bancontact}checked="checked"{/if}/>
							<label for="bancontact">{l s='Activate Bancontact (if you have Belgian customers)' mod='stripe_official'}</label><br>
							<input type="checkbox" id="sofort" name="sofort" {if $sofort}checked="checked"{/if}/>
							<label for="sofort">{l s='Activate SOFORT (if you have German, Austrian or Swiss customers)' mod='stripe_official'}</label><br>
							<input type="checkbox" id="giropay" name="giropay" {if $giropay}checked="checked"{/if}/>
							<label for="giropay">{l s='Activate Giropay (if you have German, Austrian or Swiss customers)' mod='stripe_official'}</label><br>
							<input type="checkbox" id="applepay_googlepay" name="applepay_googlepay" {if $applepay_googlepay}checked="checked"{/if}/>
							<label for="applepay_googlepay">{l s='Enable Payment Request Buttons. (Apple Pay/Chrome Payment Request API)' mod='stripe_official'}<br>{l s='By using Apple Pay, you agree to'  mod='stripe_official'} <a href="https://stripe.com/us/legal" target="blank">Stripe</a> {l s='and'  mod='stripe_official'} <a href="https://www.apple.com/legal/internet-services/terms/site.html" target="blank">Apple</a> {l s='\'s terms of service.'  mod='stripe_official'}</label>
						</div>

					</li>
					<li>
						{l s='To track correctly charges performed with these payment methods, you’ll need to add a “webhook”. A webhook is a way to be notified when an event (such as a successful payment) happens on your website.' mod='stripe_official'}
						<br><br>
						<ul>
							<li>{l s='Go on the webhook page of your Stripe dashboard:' mod='stripe_official'}
								 <a target="_blank" href="https://dashboard.stripe.com/account/webhooks">https://dashboard.stripe.com/account/webhooks</a>
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
					<li>{l s='Activate these payment methods on your' mod='stripe_official'}
						<a target="_blank" href="https://dashboard.stripe.com/account/payments/settings">{l s='Stripe dashboard.' mod='stripe_official'}</a>
					</li>
					<p>{l s='After clicking "Activate", the payment method is shown as pending with an indication of how long it might take to activate.' mod='stripe_official'}
						{l s='Once you\'ve submitted this form, the payment method will move from pending to live within 10 minutes.' mod='stripe_official'}</p>
				</ol>

				<div id="modal_applepay_googlepay" class="modal" tabindex="-1" role="dialog">
				 	<div class="modal-dialog" role="document">
				    	<div class="modal-content">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title">{l s='Informations' mod='stripe_official'}</h4>
				      		</div>
				      		<div class="modal-body">
				        		<p>{l s='You chose to enable ApplePay and GooglePay on product page.' mod='stripe_official'}</p>
				        		<p>{l s='Please be sure to have : ' mod='stripe_official'}</p>
				        		<ul>
				        			<li>{l s='Enabled guest checkout in your Back Office "Enable guest checkout"' mod='stripe_official'} <a href="{$link->getAdminLink('AdminOrderPreferences')|escape:'html':'UTF-8'}" target="blanc">{l s='(go to Preferences / Orders).' mod='stripe_official'}</a></li>
				        			<li>{l s='Google account associated with a card' mod='stripe_official'}</li>
				        			<li>{l s='ApplePay account associated with a card : To add a card to Apple Pay, you need a MacBook Pro with Touch ID. On Mac models without built-in Touch ID, you can complete your purchase using Apple Pay on your eligible iPhone or Apple Watch' mod='stripe_official'} <a href="https://support.apple.com/en-us/HT204506#macbookpro" target="blank">https://support.apple.com/en-us/HT204506#macbookpro</a></li>
				        		</ul>
				      		</div>
				      		<div class="modal-footer">
				        		<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='stripe_official'}</button>
				      		</div>
				    	</div>
				  	</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" value="1" id="configuration_form_submit_btn" name="submit_login" class="btn btn-default pull-right button">
				<i class="process-icon-save"></i>
				{l s='Enregistrer' mod='stripe_official'}
			</button>
		</div>
	</div>
</form>