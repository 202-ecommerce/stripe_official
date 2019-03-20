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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2019 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<form class="stripe-payment-form" action="#">
  <input type="hidden" name="stripe-payment-method" value="sepa_debit">
  <label>IBAN</label>
  <div id="stripe-iban-element" class="field"></div>
  <div class="stripe-error-message"></div>
  <p class="notice">By providing your IBAN and confirming this payment, you’re authorizing Payments Demo and Stripe, our payment
    provider, to send instructions to your bank to debit your account. You’re entitled to a refund under the terms
    and conditions of your agreement with your bank.</p>
</form>
