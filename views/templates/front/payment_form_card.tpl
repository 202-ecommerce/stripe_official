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

<form class="stripe-payment-form" id="stripe-card-payment">
    {if $applepay_googlepay == 'on'}
        <div id="stripe-payment-request-button"></div>

        {if isset($prestashop_version) && $prestashop_version == '1.7'}
            <div class="stripe-payment-request-button-warning modal fade">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="closer" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="modal-body">{l s='Please make sure you agreed to our Terms of Service before going any further' mod='stripe_official'}</div>
                    </div>
                </div>
            </div>
        {/if}

        <p class="card-payment-informations">{l s='Pay now with the card saved in your device by clicking on the button above or fill in your card details below and submit at the end of the page' mod='stripe_official'}</p>
    {/if}

    <input type="hidden" name="stripe-payment-method" value="card">
    <div class="stripe-error-message alert alert-danger">
      {if isset($stripeError)}<p>{$stripeError|escape:'htmlall':'UTF-8'}</p>{/if}
    </div>
    <div id="stripe-card-element" class="field"></div>

    {if isset($prestashop_version) && $prestashop_version == '1.6'}
        <button class="stripe-submit-button" data-method="card">{l s='Buy now' mod='stripe_official'}</button>
    {/if}
</form>
