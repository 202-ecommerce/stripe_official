<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 * @version   develop
 */

namespace Stripe_officialClasslib\Database\Definition\Field;

use Stripe_officialClasslib\Database\Definition\Field\FieldDefinitionColumnBuilder;

class FieldDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $definition;

    /**
     * @var string
     */
    private $column;

    /**
     * @param string $name
     * @param array $definition
     */
    public function __construct(string $name, array $definition)
    {
        $this->name = $name;
        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return FieldDefinition
     */
    public function setName(string $name): FieldDefinition
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefinition(): array
    {
        return $this->definition;
    }

    /**
     * @param array $definition
     *
     * @return FieldDefinition
     */
    public function setDefinition(array $definition): FieldDefinition
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function getColumn(): string
    {
        if (!empty($this->column)) {
            return $this->column;
        }

        $this->column = (new FieldDefinitionColumnBuilder())->buildFieldDefinition($this);

        return $this->column;
    }

    public function isLangField()
    {
        return !empty($this->getDefinition()['lang']);
    }

    public function isShopField()
    {
        return !empty($this->getDefinition()['shop']);
    }

    public function isShopLangBoth()
    {
        return $this->isShopField() && $this->getDefinition()['shop'] == 'both';
    }
}
