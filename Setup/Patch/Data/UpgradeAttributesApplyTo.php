<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netzexpert\ProductConfigurator\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\AttributeUpgradeInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class UpgradeAttributesApplyTo implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var AttributeUpgradeInterface  */
    private $attributeUpgrade;

    /**
     * UpgradeAttributesApplyTo constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeUpgradeInterface $attributeUpgrade
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeUpgradeInterface $attributeUpgrade
    ) {
        $this->moduleDataSetup  = $moduleDataSetup;
        $this->attributeUpgrade = $attributeUpgrade;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.5';
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $attributesData = [
            'name' => [
                'apply_to'  => null
            ],
            'type' => [
                'apply_to'  => null
            ],
            'description' => [
                'apply_to'  => null
            ],
            'is_required' => [
                'apply_to'  => null
            ],
            'is_visible' => [
                'apply_to'  => null
            ],
            'min_value' => [
                'apply_to'  => 'text'
            ],
            'max_value' => [
                'apply_to'  => 'text'
            ],
            'default_value' => [
                'apply_to'  => 'text'
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
            AddTextOptionSpecificAttributes::class
        ];
    }
}
