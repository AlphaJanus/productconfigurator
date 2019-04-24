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
use Netzexpert\ProductConfigurator\Setup\ConfiguratorOptionSetupFactory;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class InitialPatch implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var EavSetupFactory  */
    private $eavSetupFactory;

    /** @var ConfiguratorOptionSetupFactory  */
    private $configuratorOptionSetupFactory;

    /**
     * InitialPatch constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
    ) {
        $this->moduleDataSetup                  = $moduleDataSetup;
        $this->eavSetupFactory                  = $eavSetupFactory;
        $this->configuratorOptionSetupFactory   = $configuratorOptionSetupFactory;
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

        $fieldList = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'cost',
            'tier_price',
            'weight'
        ];

        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array(Configurator::TYPE_ID, $applyTo)) {
                $applyTo[] = Configurator::TYPE_ID;
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }

        $this->configuratorOptionSetupFactory->create(['setup' => $this->moduleDataSetup])->installEntities();
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
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.0';
    }
}
