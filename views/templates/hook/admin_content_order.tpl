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

<div class="tab-pane" id="StripePayment">
    <p>
        <span><strong>{l s='Payment ID' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl.charge|escape:'htmlall'}" target="blank">{$stripe_charge|escape:'htmlall':'UTF-8'}</a></span>
    </p>
    <p>
        <span><strong>{l s='Payment date' mod='stripe_official'}</strong></span><br/>
        <span>{$stripe_date|escape:'htmlall':'UTF-8'}</span>
    </p>
    <p>
        <span><strong>{l s='Payment method' mod='stripe_official'}</strong></span><br/>
        <span><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/cc-{$stripe_paymentType|escape:'htmlall':'UTF-8'}.png" alt="payment method" style="width:43px;"/></span>
    </p>

    {if isset($stripe_dateCatch) && $stripe_dateCatch != '0000-00-00 00:00:00'}
        <p>
            <span><strong>{l s='Authorize date' mod='stripe_official'}</strong></span><br/>
            <span>{$stripe_dateCatch|escape:'htmlall':'UTF-8'}</span>
        </p>
    {/if}

    {if (isset($stripe_dateAuthorize) && $stripe_dateAuthorize != '0000-00-00 00:00:00') || (isset($stripe_expired) && $stripe_expired == 1)}
        <p>
            <span><strong>{l s='Capture date' mod='stripe_official'}</strong></span><br/>
            {if $stripe_dateAuthorize != '0000-00-00 00:00:00'}
                <span>{$stripe_dateAuthorize|escape:'htmlall':'UTF-8'}</span>
            {else}
                <span>{l s='Expired' mod='stripe_official'}</span>
            {/if}
        </p>
    {/if}

    <p>
        <span><strong>{l s='Payment dispute' mod='stripe_official'}</strong></span><br/>
        {if $stripe_dispute === true}
            <span><a href="{$stripe_dashboardUrl.charge|escape:'htmlall'}" target="blank">{l s='check your dispute here' mod='stripe_official'}</a></span>
        {else}
            <span>{l s='No dispute' mod='stripe_official'}</span>
        {/if}
    </p>
</div>