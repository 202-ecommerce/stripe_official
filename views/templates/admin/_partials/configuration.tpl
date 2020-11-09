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
	<input type="hidden" name="order_status_select" value="{$orderStatusSelected|escape:'htmlall':'UTF-8'}">
	<div class="panel" id="fieldset_0">
		<div class="form-wrapper">
			<div class="form-group stripe-connection">
				{assign var='stripe_url' value='https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]='}
				{{l s='[a @href1@]Create your Stripe account in 10 minutes[/a] and immediately start accepting card payments as well as local payment methods (no additional contract/merchant ID needed from your bank).' mod='stripe_official'}|stripelreplace:['@href1@' => {{$stripe_url|cat:$return_url|escape:'htmlall':'UTF-8'}}, '@target@' => {'target="blank"'}]}<br>

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
				<span>
					{{l s='You can find your API keys in the Developers section of your Stripe [a @href1@]dashboard[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/account/apikeys'}, '@target@' => {'target="blank"'}]}
				</span>
			</div>

			<div class="form-group" {if $stripe_mode == 1}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Publishable key (live mode)' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_PUBLISHABLE" id="public_key" value="{$stripe_publishable|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group" {if $stripe_mode == 1}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Secret key (live mode)' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_KEY" id="secret_key" value="{$stripe_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"{if $stripe_mode == 0}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Publishable key (test mode)' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_TEST_PUBLISHABLE" id="test_public_key" value="{$stripe_test_publishable|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"{if $stripe_mode == 0}style="display: none;"{/if}>
				<label class="control-label col-lg-3 required">{l s='Secret key (test mode)' mod='stripe_official'}</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_TEST_KEY" id="test_secret_key" value="{$stripe_test_key|escape:'htmlall':'UTF-8'}" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>

			<div id="conf-payment-methods">
				<p><b>{l s='Testing Stripe' mod='stripe_official'}</b></p>
				<ul>
					<li>{l s='Toggle the button above to Test Mode.' mod='stripe_official'}</li>
					<li>
						{{l s='You\'ll find test card numbers in our [a @href1@]documentation[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'http://www.stripe.com/docs/testing'}, '@target@' => {'target="blank"'}]}
					</li>
					<li>{l s='In test mode, real cards are not accepted.' mod='stripe_official'}</li>
				</ul>
				<p><b>{l s='Going live with Stripe' mod='stripe_official'}</b></p>
				<ul>
					<li>{l s='Toggle the button above to Live Mode.' mod='stripe_official'}</li>
					<li>{l s='In live mode, tests are no longer allowed.' mod='stripe_official'}</li>
				</ul>

				<p><b>{l s='Payment form settings' mod='stripe_official'}</b></p>
				<ol item="1">
					<li>
						<p>{l s='Cards' mod='stripe_official'}</p>

						<div class="form-group">
							<input type="checkbox" id="reinsurance" name="reinsurance" {if $reinsurance}checked="checked"{/if}/>
							<label for="reinsurance">{l s='Display an extended version of the form with card logos instead of the compact version. Choose the logos to display below based on the brands accepted by your Stripe account.' mod='stripe_official'}</label><br/>
							<div class="left20">
								<input type="checkbox" id="visa" name="visa" class="child" {if $visa}checked="checked"{/if}/>
								<label for="visa">{l s='Visa' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="mastercard" name="mastercard" class="child" {if $mastercard}checked="checked"{/if}/>
								<label for="mastercard">{l s='Mastercard' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="american_express" name="american_express" class="child" {if $american_express}checked="checked"{/if}/>
								<label for="american_express">{l s='American Express' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="cb" name="cb" class="child" {if $cb}checked="checked"{/if}/>
								<label for="cb">{l s='CB (Cartes Bancaires)' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="diners_club" name="diners_club" class="child" {if $diners_club}checked="checked"{/if}/>
								<label for="diners_club">{l s='Diners Club / Discover' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="union_pay" name="union_pay" class="child" {if $union_pay}checked="checked"{/if}/>
								<label for="union_pay">{l s='China UnionPay' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="jcb" name="jcb" class="child" {if $jcb}checked="checked"{/if}/>
								<label for="jcb">{l s='JCB' mod='stripe_official'}</label><br/>
								<input type="checkbox" id="discovers" name="discovers" class="child" {if $discovers}checked="checked"{/if}/>
								<label for="discovers">{l s='Discovers' mod='stripe_official'}</label>
							</div>
						</div>

						<div class="form-group">
							<input type="checkbox" id="applepay_googlepay" name="applepay_googlepay" {if $applepay_googlepay}checked="checked"{/if}/>
							<label for="applepay_googlepay">
								{{l s='Digital wallets, i.e. Apple Pay, Google Pay and Microsoft Pay.[br]By using Apple Pay, you agree to [a @href1@]Stripe[/a] and [a @href2@]Apple[/a]\'s terms of service.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/us/legal'}, '@href2@' => {'https://www.apple.com/legal/internet-services/terms/site.html'}, '@target@' => {'target="blank"'}]}
							</label>
						</div>

						<div class="form-group">
							<input type="checkbox" id="postcode" name="postcode" {if $postcode}checked="checked"{/if}/>
							<label for="postcode">{l s='Never collect the postal code (not recommended*).' mod='stripe_official'}</label><br/>
							<span class="left20">*{l s='This information improves the acceptance rates for cards issued in the United States, the United Kingdom and Canada.' mod='stripe_official'}</span>
						</div>

						<div class="form-group">
							<input type="checkbox" id="cardholdername" name="cardholdername" {if $cardholdername}checked="checked"{/if}/>
							<label for="cardholdername">{l s='Collect the card holder name' mod='stripe_official'}</label>
						</div>

						<div class="form-group">
							<input type="checkbox" id="save_card" name="save_card" {if $save_card}checked="checked"{/if}/>
							<label for="save_card">{l s='Save customer cards (for later one-click payments)' mod='stripe_official'}</label><br/>
							<div class="left20">
								<input type="radio" name="ask_customer" id="ask_yes" value="1" class="child" {if $ask_customer == 1}checked{/if}/>
								<label for="ask_yes">{l s='Ask the customer' mod='stripe_official'}</label><br/>

								<input type="radio" name="ask_customer" id="ask_no" value="0" class="child" {if $ask_customer == 0}checked{/if}/>
								<label for="ask_no">{l s='Save without asking' mod='stripe_official'}</label>
							</div>
						</div>

						<div class="form-group">
							<input type="checkbox" id="catchandauthorize" name="catchandauthorize" {if $catchandauthorize}checked="checked"{/if}/>
							<label for="catchandauthorize">{l s='Enable separate authorization and capture. If enabled, Stripe will place a hold on the card for the amount of the order during checkout. That authorization will be captured and the money settled to your account when the order transitions to the status of your choice.' mod='stripe_official'}</label>
							<p class="left20">
								<b>{l s='Warning: you have 7 calendar days to capture the authorization before it expires and the hold on the card is released.' mod='stripe_official'}</b>
							</p>
							<span class="left20">{l s='Capture the payment when transitioning to the following order statuses.' mod='stripe_official'}</span>
							<div id="status_restrictions" class="left20">
								<br />
								<table class="table">
									<tr>
										<td class="col-md-6">
											<p>{l s='Your status' mod='stripe_official'}</p>
											<select id="order_status_select_1" class="input-large child" multiple {if $catchandauthorize == false}disabled{/if}>
												{foreach from=$orderStatus.unselected item='orderState'}
													<option value="{$orderState.id_order_state|intval}">{$orderState.name|escape}</option>
												{/foreach}
											</select>
											<a id="order_status_select_add" class="btn btn-default btn-block clearfix" >{l s='Add' mod='stripe_official'} <i class="icon-arrow-right"></i></a>
										</td>
										<td class="col-md-6">
											<p>{l s='Catch status' mod='stripe_official'}</p>
											<select id="order_status_select_2" class="input-large child" multiple {if $catchandauthorize == false}disabled{/if}>
												{foreach from=$orderStatus.selected item='orderState'}
													<option value="{$orderState.id_order_state|intval}">{$orderState.name|escape}</option>
												{/foreach}
											</select>
											<a id="order_status_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove' mod='stripe_official'} </a>
										</td>
									</tr>
								</table>
							</div>

							<div class="left20">
								<p>{l s='Transition to the following order status if the authorization expires before being captured.' mod='stripe_official'}</p>
								<select name="capture_expired" id="capture_expired" class="child" {if $catchandauthorize == false}disabled{/if}>
									<option value="0">{l s='Select a status' mod='stripe_official'}</option>
									{foreach from=$allOrderStatus item=status}
										<option value="{$status.id_order_state|intval}" {if isset($captureExpire) && $captureExpire == $status.id_order_state}selected="selected"{/if}>{$status.name|escape}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</li>
					<li>
						<p>{l s='Local payment methods' mod='stripe_official'}</p>
						<table class="table">
							<thead>
								<th class="col-md-1">{l s='Enable' mod='stripe_official'}</th>
								<th class="col-md-2">{l s='Payment method' mod='stripe_official'}</th>
								<th class="col-md-6">{l s='Relevant countries' mod='stripe_official'}</th>
								<th class="col-md-3">{l s='Require activation' mod='stripe_official'} *</th>
							</thead>
							<tbody>
								{foreach from=$payment_methods item=payment_method key=key}
									{if $payment_method.display_in_back_office}
										<tr>
											<td class="center">
												<input type="checkbox"
													   id="{$key|escape:'htmlall':'UTF-8'}"
													   name="{$key|escape:'htmlall':'UTF-8'}"
													   {if ${$key}}checked="checked"{/if}/>
											</td>
											<td>
												<span class="payment_method_name">{$payment_method.name|escape:'htmlall':'UTF-8'}</span>
												{if $payment_method.new_payment == 'Yes'}
													<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/new_payment.png" />
												{/if}
											</td>
											<td>
												{if isset($payment_method.countries_names.{$language_iso_code})}
													{$payment_method.countries_names.{$language_iso_code}|escape:'htmlall':'UTF-8'}
												{else}
													{$payment_method.countries_names.en|escape:'htmlall':'UTF-8'}
												{/if}
											</td>
											<td>
												{if $payment_method.require_activation == 'No'}
													{l s='No' mod='stripe_official'}
												{else}
													{l s='Yes' mod='stripe_official'}
												{/if}
											</td>
										</tr>
									{/if}
								{/foreach}
							</tbody>
						</table><br/>
						<p>* {{l s='You need to activate these payments methods in your [a @href2@]Stripe Dashboard[/a] first' mod='stripe_official'}|stripelreplace:['@href2@' => {{$stripe_payments_url|escape:'htmlall':'UTF-8'}}, '@target@' => {'target="blank"'}]}</p>
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