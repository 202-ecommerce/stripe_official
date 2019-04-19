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

{if isset($success)}
    {foreach from=$success item=success_message}
    	<div class="alert alert-success clearfix">
            {$success_message|escape:'htmlall':'UTF-8'}
        </div>
    {/foreach}
{/if}
{if isset($warnings)}
    {foreach from=$warnings item=warnings_message}
        <div class="alert alert-warning clearfix">
            {$warnings_message|escape:'htmlall':'UTF-8'}
        </div>
    {/foreach}
{/if}
{if isset($errors)}
    {foreach from=$errors item=errors_message}
        <div class="alert alert-danger clearfix">
            {$errors_message|escape:'htmlall':'UTF-8'}
        </div>
    {/foreach}
{/if}