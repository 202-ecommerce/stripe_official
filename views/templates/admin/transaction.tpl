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
{if $refresh == 0}
	<div class="col-lg-2" style="float:right"><a class="close refresh"><i class="process-icon-refresh" style="font-size:1em"></i></a></div>
	<script>
        var validate = "{$path|escape:'htmlall':'UTF-8'}";
        var id_employee = "{$id_employee|escape:'htmlall':'UTF-8'}";
        var token_stripe = "{$token_stripe|escape:'htmlall':'UTF-8'}";
    </script>
{/if}
<table class="table">
	<tr>
		<th>{l s='Date (last update)' mod='stripe_official'}</th>
	   	<th>{l s='Stripe Payment ID' mod='stripe_official'}</th>
	   	<th>{l s='Name' mod='stripe_official'}</th>
      <th>{l s='Card type' mod='stripe_official'}</th>
	   	<th>{l s='Amount Paid' mod='stripe_official'}</th>
	   	<th>{l s='Balance' mod='stripe_official'}</th>
	   	<th>{l s='Result' mod='stripe_official'}</th>
		<th>{l s='Mode' mod='stripe_official'}</th>
	</tr>
	{foreach from=$tenta key=k item=v}
	<tr>
		<td>{$v.date|escape:'htmlall':'UTF-8'}</td>
		<td>{$v.id_stripe|escape:'htmlall':'UTF-8'}</td>
		<td>{$v.name|escape:'htmlall':'UTF-8'}</td>
		<td><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/cc-{$v.type|escape:'htmlall':'UTF-8'}.png" alt="card type" style="width:43px;"/></td>
		<td>{$v.amount|escape:'htmlall':'UTF-8'} {$v.currency|escape:'htmlall':'UTF-8'}</td>
		<td>{$v.refund|escape:'htmlall':'UTF-8'} {$v.currency|escape:'htmlall':'UTF-8'}</td>
		{if $v.result == 2}
			<td>Refund</td>
		{elseif $v.result == 3}
			<td>Partial Refund</td>
		{else}
			<td><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/{$v.result|escape:'htmlall':'UTF-8'}ok.gif" alt="result" /></td>
		{/if}
		<td class="uppercase">{$v.state|escape:'htmlall':'UTF-8'}</td>
	</tr>
	{/foreach}
</table>
