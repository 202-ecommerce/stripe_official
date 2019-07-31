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
        <span><strong>{l s='Charge' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl.charge|escape:'htmlall'}" target="blank">{$stripe_charge}</a></span>
    </p>
    <p>
        <span><strong>{l s='Payment Intent' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl.paymentIntent|escape:'htmlall'}" target="blank">{$stripe_paymentIntent}</a></span>
    </p>
    <p>
        <span><strong>{l s='Payment date' mod='stripe_official'}</strong></span><br/>
        <span>{$stripe_date|escape:'htmlall':'UTF-8'}</span>
    </p>
    <p>
        <span><strong>{l s='Payment Type' mod='stripe_official'}</strong></span><br/>
        <span><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/cc-{$stripe_paymentType|escape:'htmlall':'UTF-8'}.png" alt="payment method" style="width:43px;"/></span>
    </p>
</div>