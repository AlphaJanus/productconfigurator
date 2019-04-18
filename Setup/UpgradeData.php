<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 23.04.18
 * Time: 12:02
 */

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class UpgradeData implements UpgradeDataInterface
{
    /** @var ConfiguratorOptionSetupFactory  */
    private $configuratorOptionSetupFactory;

    /** @var EavSetupFactory  */
    private $eavSetupFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * UpgradeData constructor.
     * @param ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory,
        EavSetupFactory $eavSetupFactory,
        LoggerInterface $logger
    ) {
        $this->configuratorOptionSetupFactory   = $configuratorOptionSetupFactory;
        $this->eavSetupFactory                  = $eavSetupFactory;
        $this->logger                           = $logger;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup  */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.23', '<')) {
            $this->upgradeVersionTwoZeroTwentyThree($setup, $eavSetup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param array $entityAttributes
     * @param ConfiguratorOptionSetup $configuratorOptionSetup
     * @return void
     */
    private function upgradeAttributes($entityAttributes, $configuratorOptionSetup)
    {
        foreach ($entityAttributes as $attributeCode => $attributeData) {
            try {
                $attribute = $configuratorOptionSetup->getEavConfig()
                    ->getAttribute(ConfiguratorOption::ENTITY, $attributeCode);
                foreach ($attributeData as $key => $value) {
                    $attribute->setData($key, $value);
                }
                $attribute->save();
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroTwentyThree($setup, $eavSetup, $context)
    {
        if (version_compare($context->getVersion(), '2.0.22', '<')) {
            $this->upgradeVersionTwoZeroTwentyTwo($setup, $eavSetup, $context);
        }

        $aplyTo = explode(
            ',',
            $eavSetup->getAttribute(Product::ENTITY, 'tax_class_id', 'apply_to')
        );
        if (!in_array(Configurator::TYPE_ID, $aplyTo)) {
            $aplyTo[] = Configurator::TYPE_ID;
            $eavSetup->updateAttribute(
                Product::ENTITY,
                "tax_class_id",
                'apply_to',
                implode(',', $aplyTo)
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroTwentyTwo($setup, $eavSetup, $context)
    {
        if (version_compare($context->getVersion(), '2.0.21', '<')) {
            $this->upgradeVersionTwoZeroTwentyOne($setup, $eavSetup, $context);
        }

        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'use_magnifier',
            [
                'group'                 => 'General',
                'type'                  => 'int',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Use magnifier?',
                'input'                 => 'boolean',
                'class'                 => '',
                'required'              => false,
                'position'              => 96,
                'default'               => '1',
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'apply_to'              => 'image',
                'source'                => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroTwentyOne($setup, $eavSetup, $context)
    {
        if (version_compare($context->getVersion(), '2.0.19', '<')) {
            $this->upgradeVersionTwoZeroNineteen($setup, $eavSetup, $context);
        }

        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'validation',
            [
                'group'                 => 'General',
                'type'                  => 'text',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Validation rules',
                'input'                 => 'text',
                'class'                 => '',
                'required'              => false,
                'position'              => 110,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'apply_to'              => 'text'
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroNineTeen($setup, $eavSetup, $context)
    {
        $tableName = $setup->getTable('catalog_product_configurator_options');
        $sql = "UPDATE " . $tableName . " SET parent_option = NULL WHERE parent_option = 0";
        $setup->getConnection()->query($sql);
        $sql = "UPDATE " . $tableName . " SET dependencies = '[]' WHERE 1";
        $setup->getConnection()->query($sql);

        $tableName = $setup->getTable('catalog_product_configurator_options_variants');
        $sql = "UPDATE " . $tableName . " SET dependencies = '[]' WHERE 1";
        $setup->getConnection()->query($sql);
        if (version_compare($context->getVersion(), '2.0.17', '<')) {
            $this->upgradeVersionTwoZeroSeventeen($setup, $eavSetup, $context);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroSeventeen(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context

    ) {
        if (version_compare($context->getVersion(), '2.0.14', '<')) {
            $this->upgradeVersionTwoZeroFourteen($setup, $eavSetup, $context);
        }
        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'extensions',
            [
                'group'                 => 'General',
                'type'                  => 'text',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Allowed Extensions',
                'input'                 => 'text',
                'class'                 => '',
                'required'              => true,
                'position'              => 100,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'apply_to'              => 'file'
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroFourteen(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '2.0.12', '<')) {
            $this->upgradeVersionTwoZeroTwelve($setup, $eavSetup, $context);
        }
        $eavSetup->updateAttribute(ConfiguratorOption::ENTITY, 'expression', 'backend_type', 'text');
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroTwelve(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context

    ) {
        if (version_compare($context->getVersion(), '2.0.8', '<')) {
            $this->upgradeVersionTwoZeroEight($setup, $eavSetup, $context);
        }
        $eavSetup->addAttribute(
            ConfiguratorOption::ENTITY,
            'add_to_price',
            [
                'group'                 => 'General',
                'type'                  => 'int',
                'backend'               => '',
                'frontend'              => '',
                'label'                 => 'Add value to price',
                'input'                 => 'boolean',
                'class'                 => '',
                'required'              => false,
                'position'              => 95,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'apply_to'              => 'expression',
                'source'                => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroEight(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgradeVersionTwoZeroFive($setup, $eavSetup, $context);
        }
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
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroFive(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context

    ) {
        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->upgradeVersionTwoZeroFour($setup, $eavSetup, $context);
        }

        /** @var ConfiguratorOptionSetup $configuratorOptionSetup */
        $configuratorOptionSetup = $this->configuratorOptionSetupFactory->create(['setup' => $setup]);
        $entityAttributes = [
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
        $this->upgradeAttributes($entityAttributes, $configuratorOptionSetup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroFour(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeVersionTwoZeroThree($setup, $eavSetup);
        }
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
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     */
    private function upgradeVersionTwoZeroThree(
        ModuleDataSetupInterface $setup,
        EavSetup $eavSetup
    ) {

        /** @var ConfiguratorOptionSetup $configuratorOptionSetup */
        $configuratorOptionSetup = $this->configuratorOptionSetupFactory->create(['setup' => $setup]);
        $eavSetup->updateEntityType(
            ConfiguratorOption::ENTITY,
            'additional_attribute_table',
            'configurator_option_eav_attribute'
        );

        $entityAttributes = [
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
        $this->upgradeAttributes($entityAttributes, $configuratorOptionSetup);
    }
}
