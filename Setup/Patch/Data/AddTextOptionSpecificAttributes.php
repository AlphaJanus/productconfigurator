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
class AddTextOptionSpecificAttributes implements
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
     * AddTextOptionSpecificAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup                  = $moduleDataSetup;
        $this->eavSetupFactory                  = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.4';
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
            'min_value',
            [
                'group'                 => 'General',
                'type'                  => 'int',
                'backend'               =>  '',
                'frontend'              => '',
                'label'                 => 'Minimum value',
                'input'                 => 'text',
                'class'                 => '',
                'required'              => false,
                'position'              => 60,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0
            ]
        );
        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'max_value',
            [
                'group'                 => 'General',
                'type'                  => 'int',
                'backend'               =>  '',
                'frontend'              => '',
                'label'                 => 'Maximum value',
                'input'                 => 'text',
                'class'                 => '',
                'required'              => false,
                'position'              => 70,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0
            ]
        );
        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'default_value',
            [
                'group'                 => 'General',
                'type'                  => 'int',
                'backend'               =>  '',
                'frontend'              => '',
                'label'                 => 'Default value',
                'input'                 => 'text',
                'class'                 => '',
                'required'              => false,
                'position'              => 80,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0
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
            UpgradeVisibleInGridOptionAttributes::class
        ];
    }
}
