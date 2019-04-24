<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netzexpert\ProductConfigurator\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class FixTaxClassIdAttribute implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var EavSetupFactory  */
    private $eavSetupFactory;

    /**
     * AddExpressionAndCodeAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup  = $moduleDataSetup;
        $this->eavSetupFactory  = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.23';
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $applyTo = explode(
            ',',
            $eavSetup->getAttribute(Product::ENTITY, 'tax_class_id', 'apply_to')
        );
        if (!in_array(Configurator::TYPE_ID, $applyTo)) {
            $applyTo[] = Configurator::TYPE_ID;
            $eavSetup->updateAttribute(
                Product::ENTITY,
                "tax_class_id",
                'apply_to',
                implode(',', $applyTo)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            AddUseMagnifierAttribute::class
        ];
    }
}
