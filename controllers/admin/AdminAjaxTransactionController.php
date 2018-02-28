<?php
/**
 * 2009-2016 202 ecommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    202 ecommerce <support@202-ecommerce.com>
 * @copyright 2009-2016 202 ecommerce SARL
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminAjaxTransactionController extends ModuleAdminController
{
	public function AjaxProcessRefresh()
	{
		/* Refresh Button Back Office on Transaction */
		if (Tools::getValue('token_stripe')) {
		    $this->ajaxDie($this->module->displayTransaction(1, Tools::getValue('token_stripe'), Tools::getValue('id_employee')));
		}
	}
}
