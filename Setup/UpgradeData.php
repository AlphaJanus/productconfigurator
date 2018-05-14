<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 23.04.18
 * Time: 12:02
 */

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption;
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
        /** @var ConfiguratorOptionSetup $configuratorOptionSetup */
        $configuratorOptionSetup = $this->configuratorOptionSetupFactory->create(['setup' => $setup]);

        /** @var EavSetup $eavSetup  */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeVersionTwoZeroThree($configuratorOptionSetup, $eavSetup);
        }
        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->upgradeVersionTwoZeroFour($eavSetup);
        }
        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgradeVersionTwoZeroFive($configuratorOptionSetup);
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
     * @param ConfiguratorOptionSetup $configuratorOptionSetup
     * @param EavSetup $eavSetup
     * @return void
     */
    private function upgradeVersionTwoZeroThree($configuratorOptionSetup, $eavSetup)
    {


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

    /**
     * @param EavSetup $eavSetup
     * @return void
     */
    private function upgradeVersionTwoZeroFour($eavSetup)
    {
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

    private function upgradeVersionTwoZeroFive($configuratorOptionSetup)
    {
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
}
