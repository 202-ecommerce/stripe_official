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

</div>
<div id="StripeAdminCart" class="panel">
    <h3>{l s='Payment information' mod='stripe_official'}</h3>
    <table class="table table-transaction">
        <thead>
            <tr>
                <th>{l s='Date (last update)' mod='stripe_official'}</th>
                <th>{l s='Stripe Payment ID' mod='stripe_official'}</th>
                <th>{l s='Name' mod='stripe_official'}</th>
                <th>{l s='Payment method' mod='stripe_official'}</th>
                <th>{l s='Amount Paid' mod='stripe_official'}</th>
                <th>{l s='Refund' mod='stripe_official'}</th>
                <th>{l s='Result' mod='stripe_official'}</th>
                <th>{l s='Mode' mod='stripe_official'}</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{$paymentInformations->date_add|escape:'htmlall':'UTF-8'}</td>
                <td><a href="{$paymentInformations->url_dashboard.paymentIntent|escape:'htmlall':'UTF-8'}" target="blank">{$paymentInformations->id_payment_intent|escape:'htmlall':'UTF-8'}</a></td>
                <td>{$paymentInformations->name|escape:'htmlall':'UTF-8'}</td>
                <td><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/cc-{$paymentInformations->type|escape:'htmlall':'UTF-8'}.png" alt="payment method" style="width:43px;"/></td>
                <td>{$paymentInformations->amount|escape:'htmlall':'UTF-8'} {$paymentInformations->currency|escape:'htmlall':'UTF-8'}</td>
                <td>{$paymentInformations->refund|escape:'htmlall':'UTF-8'} {$paymentInformations->currency|escape:'htmlall':'UTF-8'}</td>
                {if $paymentInformations->result == 2}
                    <td>{l s='Refund' mod='stripe_official'}</td>
                {elseif $paymentInformations->result == 3}
                    <td>{l s='Partial Refund' mod='stripe_official'}</td>
                {elseif $paymentInformations->result == 4}
                    <td>{l s='Waiting' mod='stripe_official'}</td>
                {else}
                    <td><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/{$paymentInformations->result|escape:'htmlall':'UTF-8'}ok.gif" alt="result" /></td>
                {/if}
                <td class="uppercase">{$paymentInformations->state|escape:'htmlall':'UTF-8'}</td>
            </tr>
        </tbody>
    </table>
</div>
<div>