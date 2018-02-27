{*
* 2007-2018 PrestaShop
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
*	@copyright	2007-2018 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<div class="panel-heading">{l s='3D-Secure' mod='stripe_official'}</div>
	<div class="commit_3d">
		<span>- {l s='For payments by Visa and MasterCard, you can add an additional layer of security by enforcing 3D-Secure authentification.' mod='stripe_official'}</span>
		<br>
		- {l s='3D-Secure (Verified by VISA, MasterCard SecureCode™) is a system that is used to verify a customer’s identity before an online purchase can be completed, so that merchants can reduce fraud.' mod='stripe_official'}
		<br>- {l s='With 3D-Secure, customers are redirected to a page provided by their bank, where they are prompted to enter an additional password before their card can be charged.' mod='stripe_official'}
		<br>- {l s='You can learn more about 3D-Secure on our website: ' mod='stripe_official'}<a href="https://support.stripe.com/questions/does-stripe-support-3d-secure-verified-by-visa-mastercard-securecode" target="_blank">https://support.stripe.com/questions/does-stripe-support-3d-secure-verified-by-visa-mastercard-securecode</a>
	</div>
</div>

{$secure_form}

