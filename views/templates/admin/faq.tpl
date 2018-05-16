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

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='THANKS FOR CHOOSING STRIPE' mod='stripe_official'}</h3>
<div class="form-group">
    <br>
    {l s='If you run into any issue after having installed this plugin, please first read our below FAQ and make sure :' mod='stripe_official'}
    <br>
    <ol type="1">
        <li>{l s='You have entered your API keys in the “Connection” tab of the Stripe module (we recommend checking that there is no space in the field).' mod='stripe_official'}</li>
        <li>{l s='You are using test cards in Test mode and live cards in Live mode.' mod='stripe_official'}</li>
        <li>{l s='If you’ve recently updated the module, you have refreshed your cache.' mod='stripe_official'}</li>
        <li>{l s='You’re not using any other plugin that could impact payments.' mod='stripe_official'}</li>
    </ol>
    <br>
    {l s='You can also check out our support website:' mod='stripe_official'}
    <a href="https://support.stripe.com" target="_blank">https://support.stripe.com</a>
    <br><br>
    {l s='If you have any additional question or remaining issue related to Stripe and this plugin, please contact our support team:' mod='stripe_official'}
    <a href="https://support.stripe.com/email" target="_blank">https://support.stripe.com/email</a>
</div>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='Frequently Asked Questions' mod='stripe_official'}</h3>
 <div class="faq items">

	  <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='What are the required elements to use the module?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='To use this module and process credit card payments, you will need to have the following before going any further:' mod='stripe_official'}
                </p>

                <ul>
                    <li>
                        {l s='An installed SSL certificate. In order to get it, please contact your web hosting service or a SSL certificate provider.' mod='stripe_official'}
                    </li>

                    <li>
                        {l s='A PHP version >= 5.3.3 environment (Stripe prerequisite). If you have an older PHP version, please ask your hosting provider to' mod='stripe_official'}
                        {l s='upgrade it to match the requirement.' mod='stripe_official'}
                    </li>
                </ul>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I get Stripe test secret and publishable keys for the connection tab?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                {l s='First, you need to create and administrate a Stripe account. Then, you’ll find your API keys located in your account settings.' mod='stripe_official'}
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What is Stripe pricing?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='For European companies, Stripe charges (per successful transaction): ' mod='stripe_official'}<br>
                    - {l s='1.4% + €0.25/£0.20 with a European card ' mod='stripe_official'}<br>
                    - {l s='2.9% + €0.25/£0.20 with a non-European card' mod='stripe_official'}<br>
                    {l s='Stripe has no setup fees, no monthly fees, no validation fees, no refund fees, and no card storage fees. There’s no additional fee for failed charges or refunds.' mod='stripe_official'}<br>
                </p>

                <p>
                    {l s='If you’d like to learn more about our simple pricing, please check our website: ' mod='stripe_official'}
                    <a href="http://stripe.com/pricing" target="_blank">http://stripe.com/pricing</a><br>
                    {l s='We offer customized pricing for larger businesses. If you accept more than €30,000 per month,' mod='stripe_official'}
                    <a target="_blank" href="https://stripe.com/contact/sales"> {l s='get in touch' mod='stripe_official'}</a>
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What is the difference between Test and Live Mode?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Every account is divided into two universes: one for testing, and one for running on your live website.' mod='stripe_official'}
                </p>

                <p>
                    {l s='In test mode, credit card transactions don\'t go through the actual credit card network — instead, they go through simple checks in' mod='stripe_official'}
                    {l s='Stripe to validate that they look like they might be credit cards.' mod='stripe_official'}
                </p>

                <p>
                    {l s='In order to activate Live mode, you only need to click No in “Test mode” configuration.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I make test payments using Stripe payment gateway on my store?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='When the module is in test mode, you are able to click any of the credit card buttons (VISA, MasterCard, etc. logos) on the' mod='stripe_official'}
                    {l s='payment page to generate a sample credit card number for testing purposes.' mod='stripe_official'}
                </p>
            </div>
        </li>


	  </ul>
</div>
