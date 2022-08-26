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

<p><b>{l s='Congratulations, your order has been placed and will be processed soon.' mod='stripe_official'}</b><br /><br />

{{l s='Your order reference is [b]@target@[/b], you should receive a confirmation e-mail shortly.' mod='stripe_official'}|stripelreplace:['@target@' => {{$stripe_order_reference|escape:'htmlall'}}] nofilter}<br /><br />

{if $stripePayment->type == 'oxxo'}
    {{l s='Your can see your OXXO voucher [a @href1@]here[/a].' mod='stripe_official'}|stripelreplace:['@href1@' => {{$stripePayment->voucher_url|escape:'htmlall'}}, '@target@' => {'target="blank"'}] nofilter}<br /><br />
{/if}

{l s='We appreciate your business.' mod='stripe_official'}<br /><br /></p>