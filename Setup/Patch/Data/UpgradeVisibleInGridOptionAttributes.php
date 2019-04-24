<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netzexpert\ProductConfigurator\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\AttributeUpgradeInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class UpgradeVisibleInGridOptionAttributes implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var EavSetupFactory  */
    private $eavSetupFactory;

    /** @var AttributeUpgradeInterface  */
    private $attributeUpgrade;

    /**
     * UpgradeVisibleInGridOptionAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeUpgradeInterface $attributeUpgrade
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        AttributeUpgradeInterface $attributeUpgrade
    ) {
        $this->moduleDataSetup                  = $moduleDataSetup;
        $this->eavSetupFactory                  = $eavSetupFactory;
        $this->attributeUpgrade                 = $attributeUpgrade;
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

        $eavSetup->updateEntityType(
            ConfiguratorOption::ENTITY,
            'additional_attribute_table',
            'configurator_option_eav_attribute'
        );

        $attributesData = [
            'name' => [
                'sort_order'            => 10,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
            ],
            'type' => [
                'sort_order'            => 20,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
            ],
            'description' => [
                'sort_order'            => 30,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 0,
            ],
            'is_required' => [
                'sort_order'            => 40,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
            ],
            'is_visible' => [
                'sort_order'            => 50,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
            ]
        ];

        $this->attributeUpgrade->execute($attributesData);
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
            InitialPatch::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.3';
    }
}
