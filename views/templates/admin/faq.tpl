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

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='Frequently Asked Questions' mod='prestastripe'}</h3>
 <div class="faq items">

	  <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='What are the required elements to use the module?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='To use this module and process credit card payments, you will need to have the following before going any further:' mod='prestastripe'}
                </p>

                <ul>
                    <li>
                        {l s='An installed SSL certificate. In order to get it, please contact your web hosting service or a SSL certificate provider.' mod='prestastripe'}
                    </li>

                    <li>
                        {l s='A PHP version >= 5.3.3 environment (Stripe prerequisite). If you have an older PHP version, please ask your hosting provider to' mod='prestastripe'}
                        {l s='upgrade it to match the requirement.' mod='prestastripe'}
                    </li>
                </ul>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I get Stripe test secret and publishable keys for the connection tab?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                {l s='First, you need to create and administrate a Stripe account. Then, you’ll find your API keys located in your account settings.' mod='prestastripe'}
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What is Stripe pricing?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='For most countries, Stripe charges 2.9% + 30c per successful charge, or less based on your volume.' mod='prestastripe'}
                </p>

                <p>
                    {l s='But there are different exceptions, so we invite you to consult' mod='prestastripe'}
                    <a href="http://stripe.com/pricing" target="_blank">http://stripe.com/pricing</a>
                    {l s='and select your country on the list at the bottom left of the page.' mod='prestastripe'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What is the difference between Test and Live Mode?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Every account is divided into two universes: one for testing, and one for running on your live website.' mod='prestastripe'}
                </p>

                <p>
                    {l s='In test mode, credit card transactions don\'t go through the actual credit card network — instead, they go through simple checks in' mod='prestastripe'}
                    {l s='Stripe to validate that they look like they might be credit cards.' mod='prestastripe'}
                </p>

                <p>
                    {l s='In order to activate Live mode, you only need to click No in “Test mode” configuration.' mod='prestastripe'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I make test payments using Stripe payment gateway on my store?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='When the module is in test mode, you are able to click any of the credit card buttons (VISA, MasterCard, etc. logos) on the' mod='prestastripe'}
                    {l s='payment page to generate a sample credit card number for testing purposes.' mod='prestastripe'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What are the Transaction and Refund tabs useful for?' mod='prestastripe'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='In the Transactions tab, all transactions processed with Stripe will be displayed with the date, ID, Name of the client, card' mod='prestastripe'}
                    {l s='information, amount paid, balance and result.' mod='prestastripe'}
                </p>

                <p>
                    {l s='In the Refund tab, you will be able to process a total or partial refund to orders of your choice. You only need to fill in the' mod='prestastripe'}
                    {l s='order Stripe payment ID located in the Transactions tab. Click on Request Refund button, then choose “Total Refund” or “Partial' mod='prestastripe'}
                    {l s='Refund” with the desired refund amount and click on the Submit button.' mod='prestastripe'}
                </p>
            </div>
        </li>
	  </ul>
</div>
