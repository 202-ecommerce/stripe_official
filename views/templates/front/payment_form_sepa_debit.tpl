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

<form class="stripe-payment-form" id="stripe-sepa-element" action="">
    <input type="hidden" name="stripe-payment-method" value="sepa_debit">

    {if isset($prestashop_version) && $prestashop_version == '1.7'}
        <div id="stripe-iban-element" class="field"></div>
    {/if}

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
        <div class="payment_module stripe-europe-payments" data-method="sepa_debit">
            <p title="{l s='Pay by SEPA Direct Debit' mod='stripe_official'}">
                <img id="sepa_debit" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/sepa_debit.png" alt="{l s='Pay by SEPA Direct Debit' mod='stripe_official'}" />
                {l s='Pay by SEPA Direct Debit' mod='stripe_official'}
            </p>
        </div>
        <span id="stripe-iban-element" class="field"></span>
        <button class="sepa_debit-submit-button" data-method="sepa_debit">{l s='Buy now' mod='stripe_official'}</button>
    {/if}

</form>
