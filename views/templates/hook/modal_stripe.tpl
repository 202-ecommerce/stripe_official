{*
* 2007-2017 PrestaShop
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
*	@copyright	2007-2017 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
<script>
    var stripe_payment_error = "{$stripe_error|escape:'htmlall':'UTF-8'}";
    var stripe_type_error = "{$payment_error_type|escape:'htmlall':'UTF-8'}";
</script>
<div class="stripe-error payment-error-{$payment_method|escape:'htmlall':'UTF-8'}"></div>
<div id="modal_stripe_waiting"  class="modal" style="display: none">
    <div id="stripe-ajax-loader-europe"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" />&nbsp; {l s='Transaction in progress, please wait.' mod='stripe_official'}</div>
</div>