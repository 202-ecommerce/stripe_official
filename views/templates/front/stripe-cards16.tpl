{**
 * 2007-2022 Stripe
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
 * @license   Academic Free License (AFL 3.0)
 *}
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='stripe_official'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My cards' mod='stripe_official'}</span>{/capture}

<h1 class="page-heading bottom-indent">
    {l s='My cards' mod='stripe_official'}
</h1>
<div class="block-center">
    {if $cards|@count > 0}
      <table class="table table-striped table-bordered table-labeled table-responsive-lg">
        <thead class="thead-default">
        <tr>
          <th class="text-center">{l s='Type' mod='stripe_official'}</th>
          <th class="text-center">{l s='Card number' mod='stripe_official'}</th>
          <th class="text-center">{l s='Validity' mod='stripe_official'}</th>
          <th class="text-center">{l s='Delete' mod='stripe_official'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$cards item=card}
          <tr>
            <td class="text-center" data-label="{l s='Type' mod='stripe_official'}">
                {$card.card->brand|capitalize|escape:'htmlall':'UTF-8'}
            </td>
            <td class="text-center" data-label="{l s='Card number' mod='stripe_official'}">
              **** **** **** {$card.card->last4|escape:'htmlall':'UTF-8'}
            </td>
            <td class="text-center" data-label="{l s='Validity' mod='stripe_official'}">
                {$card.card->exp_month|escape:'htmlall':'UTF-8'}/{$card.card->exp_year|escape:'htmlall':'UTF-8'}
            </td>
            <td class="text-center" data-label="{l s='Type' mod='stripe_official'}">
                        <span class="remove_card" data-id_payment_method="{$card.id|escape:'htmlall':'UTF-8'}">
                            <i class="icon-trash-o"></i>
                        </span>
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    {else}
      <p>{l s='You haven\'t registered a card yet.' mod='stripe_official'}</p>
    {/if}
</div>
