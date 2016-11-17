{*
* 2007-2016 PrestaShop
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
*	@copyright	2007-2016 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div class="tabs">

  <div class="sidebar navigation col-md-2">
	{if isset($tab_contents.logo)}
	  <img class="tabs-logo" src="{$tab_contents.logo|escape:'htmlall':'UTF-8'}"/>
	{/if}
	<nav class="list-group categorieList">
	  {foreach from=$tab_contents.contents key=tab_nbr item=content}
		<a class="list-group-item migration-tab"
		   href="#stripe_step_{$tab_nbr + 1|intval}">

		  {if isset($content.icon) && $content.icon != false}
			<i class="{$content.icon|escape:"htmlall":"UTF-8"} pstab-icon"></i>
		  {/if}

		  {$content.name|escape:"htmlall":"UTF-8"}

		  {if isset($content.badge) && $content.badge != false}
			<span class="badge-module-tabs pull-right {$content.badge|escape:"htmlall":"UTF-8"}"></span>
		  {/if}

		</a>
	  {/foreach}
	</nav>
  </div>

  <div class="col-md-10">
	<div class="content-wrap panel">
	  {foreach from=$tab_contents.contents key=tab_nbr item=content}
		<section id="section-shape-{$tab_nbr + 1|intval}">{$content.value|escape:"entity":"UTF-8"}</section>
		  <!--html code generated-->
	  {/foreach}
	</div>
  </div>

</div>
<script type="text/javascript">
	var Translation = [];
	Translation[0] = "{l s='3D-Secure (Verified by VISA, MasterCard SecureCode™) is a system that is used to verify a customer’s identity before an online purchase can be completed, so that merchants can reduce fraud.' mod='stripe_official'}";
	Translation[1] = "{l s='With 3D-Secure, customers are redirected to a page provided by their bank, where they are prompted to enter an additional password before their card can be charged.' mod='stripe_official'}";
	Translation[2] = "{l s='You can learn more about 3D-Secure on our website: ' mod='stripe_official'}";
</script>
<script type="text/javascript" src="{$new_base_dir|escape:'htmlall':'UTF-8'}views/js/PSTabs.js"></script>
<script type="text/javascript">
		(function() {
		[].slice.call(document.querySelectorAll('.tabs')).forEach(function(el) {
			new PSTabs(el);
		});
	})();
</script>