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

<form class="stripe-payment-form" id="stripe-oxxo-element" action="">
    <input type="hidden" name="stripe-payment-method" value="oxxo">

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
      <div class="payment_module" data-method="oxxo">
        <p title="{l s='Pay by Oxxo' mod='stripe_official'}">
          <img id="ideal" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/oxxo.png" alt="{l s='Pay by iDeal' mod='stripe_official'}" />
          &nbsp;{l s='Pay by Oxxo' mod='stripe_official'}
        </p>
      </div>
    {/if}

    <div class="form-row">
        <input id="oxxo-name" name="oxxo-name" placeholder="{l s='Name' mod='stripe_official'}" required>
    </div>

    <div class="form-row">
        <input id="oxxo-email" name="oxxo-email" placeholder="{l s='Email' mod='stripe_official'}" required>
    </div>

    <!-- Used to display form errors. -->
    <div id="error-message" role="alert"></div>

    <div class="stripe-error-message alert alert-danger"></div>

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
      <button class="stripe-submit-button" data-method="oxxo">{l s='Buy now' mod='stripe_official'}</button>
    {/if}
</form>
