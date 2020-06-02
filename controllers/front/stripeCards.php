<?php
/**
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
 */

class stripe_officialStripeCardsModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $allCards = array();

        if ($this->context->customer->id != null) {
            $stripeCustomer = new StripeCustomer();
            $stripeCustomer->getCustomerById($this->context->customer->id);

            if ($stripeCustomer->id != null) {
                $stripeCard = new StripeCard($stripeCustomer->stripe_customer_key);
                $allCards = $stripeCard->getAllCustomerCards();

                foreach ($allCards as &$card) {
                    $card->card->exp_month = sprintf('%02d', $card->card->exp_month);
                    $card->card->exp_year = Tools::substr($card->card->exp_year, -2);
                }
            }
        }

        $this->context->smarty->assign(array(
            'cards' => $allCards
        ));

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setTemplate('module:stripe_official/views/templates/front/stripe-cards.tpl');
        } else {
            $this->setTemplate('stripe-cards16.tpl');
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->l('Cards', 'stripe_official'),
            'url' => $this->context->link->getModuleLink(
                'stripe_official',
                'stripeCards',
                array(),
                true
            ),
        ];
        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        Media::addJsDef(array(
            'stripe_remove_card_url' => $this->context->link->getModuleLink(
                'stripe_official',
                'removeCard',
                array(),
                true
            )
        ));

        $this->addJS(_MODULE_DIR_ . 'stripe_official/views/js/stripeCard.js');
    }
}
