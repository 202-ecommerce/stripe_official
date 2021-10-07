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

namespace Stripe_officialClasslib\Database\Definition\Table;

use Stripe_officialClasslib\Database\Definition\ObjectModel\ObjectModelDefinition;

abstract class AbstractTableDefinitionBuilder
{
    /**
     * @var ObjectModelDefinition
     */
    protected $objectModelDefinition;

    /**
     * @param ObjectModelDefinition $objectModelDefinition
     */
    public function __construct(ObjectModelDefinition $objectModelDefinition)
    {
        $this->objectModelDefinition = $objectModelDefinition;
    }

    /**
     * @return TableDefinition
     */
    public function build()
    {
        $tableDefinition = new TableDefinition();

        $this->buildCommonFields($tableDefinition)
            ->buildSpecificFields($tableDefinition);

        return $tableDefinition;
    }

    protected function buildCommonFields(TableDefinition $tableDefinition)
    {
        $tableDefinition->setCharset($this->objectModelDefinition->getCharset())
            ->setCollation($this->objectModelDefinition->getCollation())
            ->setEngine($this->objectModelDefinition->getEngine())
            ->setFields($this->getColumns());

        return $this;
    }

    /**
     * @param TableDefinition $tableDefinition
     *
     * @return AbstractTableDefinitionBuilder
     */
    abstract protected function buildSpecificFields(TableDefinition $tableDefinition);

    abstract protected function getColumns();
}
