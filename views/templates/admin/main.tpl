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

{include file="./_partials/messages.tpl"}
<div class="tabs">
	<div class="sidebar navigation col-md-2">
		{if isset($logo)}
		  <img class="tabs-logo" src="{$logo|escape:'htmlall':'UTF-8'}"/>
		{/if}
		<nav class="list-group categorieList">
			<a class="list-group-item migration-tab" href="#stripe_step_1">
			  	<i class="icon-power-off pstab-icon"></i>
			  	{l s='Connection' mod='stripe_official'}
			  	<span class="badge-module-tabs pull-right {if $keys_configured === true}tab-success{else}tab-warning{/if}"></span>
			</a>
			<a class="list-group-item migration-tab" href="#stripe_step_2">
			  	<i class="icon-ticket pstab-icon"></i>
			  	{l s='Refund' mod='stripe_official'}
			</a>
		</nav>
	</div>

	<div class="col-md-10">
		<div class="content-wrap panel">
			<section id="section-shape-1">
				{include file="./_partials/configuration.tpl"}
			</section>
			<section id="section-shape-2">
				{include file="./_partials/refund.tpl"}
			</section>
		</div>
	</div>

</div>