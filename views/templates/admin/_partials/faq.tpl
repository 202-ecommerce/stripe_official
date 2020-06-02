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

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='THANKS FOR CHOOSING STRIPE' mod='stripe_official'}</h3>
<div class="form-group">
    <br>
    {l s='If you run into any issue after installing this module, please read our FAQ below and make sure that:' mod='stripe_official'}
    <br>
    <ol type="1">
        <li>{l s='You configured your API keys in the "Connection" tab (make sure there is no extra space in the field).' mod='stripe_official'}</li>
        <li>{l s='You refreshed your cache if you recently updated the module.' mod='stripe_official'}</li>
        <li>{l s='You\'re not using another module that might conflict with payment modules.' mod='stripe_official'}</li>
    </ol>
    <br>
    {l s='You can also check out our support website:' mod='stripe_official'}
    <a href="https://support.stripe.com" target="_blank">https://support.stripe.com</a>
    <br><br>
    {l s='If you have any additional question or remaining issue related to Stripe and this plugin, please contact our support team:' mod='stripe_official'}
    <a href="https://support.stripe.com/email" target="_blank">https://support.stripe.com/email</a>
</div>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='Improve your conversion rate and securely charge your customers with Stripe, the easiest payment platform' mod='stripe_official'}</h3>
<div class="form-group stripe-module-col1inner">
    - {{l s='[b]Improve your conversion rate[/b] by offering a seamless payment experience to your customers: Stripe lets you host the payment form on your own pages, without redirection to a bank third-part page.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/radar'}, '@target@' => {'target="blank"'}]}<br>
    - {{l s='[b]Keep your fraud under control[/b] thanks to customizable 3D-Secure and [a @href1@]Stripe Radar[/a], our suite of anti-fraud tools.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/radar'}, '@target@' => {'target="blank"'}]}<br>
    - {{l s='[b]Easily refund[/b] your orders through your PrestaShop’s back-office (and automatically update your PrestaShop order status).' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/radar'}, '@target@' => {'target="blank"'}]}<br>
    - {{l s='Start selling abroad by offering payments in [b]135+ currencies[/b] and 4 local payment methods (iDEAL, Bancontact, SOFORT, Giropay).' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/radar'}, '@target@' => {'target="blank"'}]}<br><br>
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/started.png" style="width:100%;">
    <br><br>
    <p>
        {{l s='Find out more about Stripe on our website: [a @href1@]www.stripe.com[/a]' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/fr'}, '@target@' => {'target="blank"'}]}
    </p>
</div>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> {l s='Frequently Asked Questions' mod='stripe_official'}</h3>
<div class="faq items">
    <span class="faq-title">{l s='General' mod='stripe_official'}</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='Do I need a Stripe account to use this module?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='Yes. It takes only a few minutes to sign up and it\'s free: [a @href1@]https://dashboard.stripe.com/register[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/register'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Can I test before creating an account?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Unfortunately, you have to use your own account. Again, this is quick & free and doesn\'t engage you to anything. You can use the test mode and never go live if you are not satisfied with our solutions!' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How much will it cost me?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='Downloading, installing and testing this module is entirely free. You will only get charged once you start processing live payments. You can learn more about our pricing model online: [a @href1@]https://stripe.com/pricing[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/pricing'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title">{l s='Features' mod='stripe_official'}</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='What payment methods are supported?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='This module supports card payments, Apple Pay, Google Pay, Microsoft Pay, Bancontact, iDeal, Giropay and Sofort.' mod='stripe_official'}<br>
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Why are some Stripe features not supported by this module?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                {l s='Implementing features in this module requires time for development, testing and releasing. We started with what we felt was more likely to cover most of the merchants and customers needs. We will be adding the missing features one by one in the future.' mod='stripe_official'}
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Can I ask you to add a new feature?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='We don\'t take feature requests at the moment. If you still feel like your suggestion could benefit all our PrestaShop users, feel free to reach out to us: [a @href1@]https://support.stripe.com/contact[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://support.stripe.com/contact'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Is Stripe Radar supported?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Yes, if available for your Stripe account, you can use Stripe Radar with this module.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Are card payments compatible with the new Strong Customer Authentication requirement and 3DS v2?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Yes, starting from the version 2.0 of this module, all card payments are compatible with 3DS v2 and SCA ready.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Is Stripe Billing supported?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='No, Stripe Billing is not currently supported.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Is Stripe Connect supported?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='No, Stripe Connect is not currently supported.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I implement a new feature myself?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='If you\'re a developer, you can follow our online guides and documentation: [a @href1@]https://stripe.com/docs[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs'}, '@target@' => {'target="blank"'}]}<br/>
                    {{l s='Otherwise, you can look for a developer or an agency specialised in PrestaShop: [a @href1@]https://www.prestashop.com/experts[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://www.prestashop.com/experts'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Can customers choose to save their card for faster later checkout?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Yes, since version 2.1 of the module, customers can save one or multiple cards during checkout. Cards can be deleted directly from their account settings.' mod='stripe_official'}<br/>
                    {l s='You can enable this feature in the module settings.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Can I just authorize card payments during checkout and later on debit the amount?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Yes, since version 2.1 of the module, you can choose in the settings to only debit the authorization on a specific order status. Note that you have 7 calendar days to debit the card after the authorization. After this delay, the hold on the card is released and the customer has to restart the checkout process to confirm the order.' mod='stripe_official'}
                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title">{l s='Installation' mod='stripe_official'}</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='What are the requirements?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - {l s='PrestaShop 1.6 or higher' mod='stripe_official'}<br/>
                    - {l s='PHP 5.6 or higher' mod='stripe_official'}<br/>
                    - {l s='TLS 1.2 (live mode)' mod='stripe_official'}<br/>
                    - {l s='A Stripe account' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Are there any known incompatibilities with other modules?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - {l s='PrestaShop is a highly customisable online shop solution with many ready to use extensions in order to fit your specific needs.' mod='stripe_official'}<br>
                    - {l s='Knowing this, it is impossible to guarantee that our module will work with all customised shops.' mod='stripe_official'}<br>
                    - {l s='This module is compatible with most of the existing modules. The only exception concerns modules altering the standard behavior of the checkout flow. If you have such modules installed in your shop, we recommend you try our module in your test environment first.' mod='stripe_official'}<br>
                    - {l s='In case there is an incompatibility, you should reach out to your developer to make our module compatible with your shop.' mod='stripe_official'}<br>
                    - {l s='There is a code compatibility problem with some modules that modify the checkout funnel. Stripe provides the necessary hooks for the other modules, but some of them are not able to read the hooks correctly.' mod='stripe_official'}<br>
                    - {l s='We encountered this problem with the following modules (non-exhaustive list):' mod='stripe_official'}<br>
                    <ul>
                        <li>{l s='The checkout' mod='stripe_official'}</li>
                        <li>{l s='One Page Checkout, Social Login & Mailchimp' mod='stripe_official'}</li>
                    </ul>
                    {l s='These modules have encountered problems with the 2.0.7 version of Stripe in October 2019, it’s possible that evolutions have been made to these modules since that date making them compatible with the Stripe module.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='What are test and live modes?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - {l s='Test mode allows you to validate that the module works well in your shop and to see what the user experience feels like without actually debiting any money nor triggering any cost to you.' mod='stripe_official'}<br>
                    - {l s='Once ready to charge your customers with our module, you can switch to live mode.' mod='stripe_official'}<br>
                    - {l s='Test and live modes are distinguished by different sets of API keys.' mod='stripe_official'}<br>
                    - {{l s='For more information: [a @href1@]https://stripe.com/docs/keys[/a] and [a @href2@]https://stripe.com/docs/testing[/a]\'s terms of service.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs/keys'}, '@href2@' => {'https://stripe.com/docs/testing'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I check if my installation is successful?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='Configure your test mode API keys (see [a @href1@]https://stripe.com/docs/keys[/a]), activate your preferred payment methods and go through the consumer checkout flow.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs/keys'}, '@target@' => {'target="blank"'}]}
                    {{l s='You can use our testing card numbers: [a @href1@]https://stripe.com/docs/testing[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs/testing'}, '@target@' => {'target="blank"'}]}<br/>
                    {l s='If the module works well in test mode, it should work just as well in live mode.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='How can I configure my 3DS preferences?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='You can use Stripe Radar, our anti-fraud module to fine-tune your protection needs directly from your Stripe Dashboard: [a @href1@]https://stripe.com/radar[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/radar'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Add my domain ApplePay manually from my dashboard in order to use ApplePay' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='You can manually add your domain(s) throught your Dashboard. You can easily do this by following those five steps once you are logged into your Dashboard : ' mod='stripe_official'}<br/>
                    A : {l s='Go to the "Payments" menu.' mod='stripe_official'}<br/>
                    B : {l s='Click on Apple Pay.' mod='stripe_official'}<br/>
                    C : {l s='Click on the button "Add new domain".' mod='stripe_official'}<br/>
                    D : {l s='Provide the domain by filling the input.' mod='stripe_official'}<br/>
                    ({l s='Additional step : make sure you have the file "apple-developer-merchantid-domain-association" on the root of your website in the folder ".well-known/". If no, please follow the steps 2 and 3 in the popup "Add new domain."' mod='stripe_official'})<br/>
                    E : {l s='Add your domain by clicking thebutton "Add".' mod='stripe_official'}<br/><br/>
                    <img class="add_domain" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/stripe_add_domain.png" />
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Do I need to configure Stripe webhooks?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Since version 2.1 of the module, you do not need to manually create the webhook in your Stripe Dashboard. If you already created the webhook the module will automatically refresh it and no further action is requested from you.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='Why do you need webhooks?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='Some payment actions are asynchronous and webhooks are the only way to keep your orders up to date automatically.' mod='stripe_official'}
                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title">{l s='Troubleshooting problems' mod='stripe_official'}</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger">{l s='I activated Apple Pay / Google Pay, why can\'t I see the Pay button?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - {l s='Make sure that your host supports TLS 1.2.' mod='stripe_official'}<br/>
                    - {{l s='For Apple Pay, you also need to get your domain verified by Apple (see [a @href1@]https://stripe.com/docs/apple-pay/web/v2#going-live[/a]).' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs/apple-pay/web/v2#going-live'}, '@target@' => {'target="blank"'}]}<br/>
                    - {l s='Check that you have a payment card saved in your device/browser.' mod='stripe_official'}<br/>
                    {{l s='If the button still doesn\'t show, please contact our developers: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://addons.prestashop.com/en/contact-us?id_product=24922'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='My customer can\'t/couldn\'t pay, how can I help him?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='You can check your Stripe Dashboard to see if his payment has been declined: [a @href1@]https://dashboard.stripe.com/payments[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/payments'}, '@target@' => {'target="blank"'}]}<br>
                    {{l s='If you don\'t find a trace of any payment attempt for that customer, there might have been a technical issue. Please reach out to our developers: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://addons.prestashop.com/en/contact-us?id_product=24922'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='My customer already provided his payment details, can I debit him myself for future orders?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {{l s='Stripe allows payment methods to be securely saved against your customers for future use: [a @href1@]https://stripe.com/docs/payments/payment-methods/saving[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://stripe.com/docs/payments/payment-methods/saving'}, '@target@' => {'target="blank"'}]}<br/>
                    {l s='Unfortunately, this module doesn\'t support this feature yet. Thus, your customer has to enter his payment details for any new payment via the usual checkout flow.' mod='stripe_official'}
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger">{l s='My customer paid but the order status has not been updated, what should I do?' mod='stripe_official'}</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    {l s='A technical issue might have occured between the payment and the order validation.' mod='stripe_official'}<br/>
                    {{l s='You can check your Stripe Dashboard to confirm that the customer has indeed been debited ([a @href1@]https://dashboard.stripe.com/payments[/a]) and update the order manually in your shop\'s back office.' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://dashboard.stripe.com/payments'}, '@target@' => {'target="blank"'}]}<br/>
                    {{l s='If this occurs more than once, you may want to ask your developer to investigate for any Javascript or PHP errors occuring during the checkout flow and eventually reach out to our developers for help: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {'https://addons.prestashop.com/en/contact-us?id_product=24922'}, '@target@' => {'target="blank"'}]}
                </p>
            </div>
        </li>
    </ul>
</div>