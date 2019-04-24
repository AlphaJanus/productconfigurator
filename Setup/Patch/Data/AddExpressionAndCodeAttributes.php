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

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class AddExpressionAndCodeAttributes implements
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
        return '2.0.8';
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

        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'expression',
            [
                'group'                 => 'General',
                'type'                  => 'varchar',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Expression',
                'input'                 => 'textarea',
                'class'                 => '',
                'required'              => true,
                'position'              => 90,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'apply_to'              => 'expression'
            ]
        );
        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'code',
            [
                'group'                 => 'General',
                'type'                  => 'static',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Code',
                'input'                 => 'text',
                'class'                 => 'validate-data',
                'unique'                => true,
                'required'              => true,
                'position'              => 90,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
            ]
        );
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
            UpgradeAttributesApplyTo::class
        ];
    }
}
