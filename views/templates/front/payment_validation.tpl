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

<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
</head>

<body>
{hook h='displayAfterBodyOpeningTag'}
<main>

<p><h2>{l s='Transaction in progress, please wait.' mod='stripe_official'}</h2></p>

<div id="modal_stripe_waiting"  class="modal" style="display: none">
    <div id="stripe-ajax-loader-europe"><img src="/modules/stripe_official/views/img/ajax-loader.gif" alt="" />&nbsp;<br> <br>{l s='Transaction in progress, please wait.' mod='stripe_official'}</div>
</div>

<script>
    var stripe_client_secret = "{$stripe_client_secret}";
    var stripe_source = "{$stripe_source}";
    var StripePubKey = "{$publishableKey}";
    var ajaxUrlStripe = "{$ajaxUrlStripe}";
    var return_order_page = "{$return_order_page}";
</script>


    <!-- Footer Ends -->
    {block name='javascript_bottom'}
        {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}
    {hook h='displayBeforeBodyClosingTag'}
</main>

</body>