<?php

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;

class InstallData implements InstallDataInterface
{
    /** @var EavSetupFactory  */
    protected $eavSetupFactory;

    /** @var ConfiguratorOptionSetupFactory  */
    protected $configuratorOptionSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
    ) {
        $this->eavSetupFactory                  = $eavSetupFactory;
        $this->configuratorOptionSetupFactory   = $configuratorOptionSetupFactory;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

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
            $aplyTo = explode(
                ',',
                $eavSetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if(!in_array(Configurator::TYPE_ID,$aplyTo)){
                $aplyTo[] = Configurator::TYPE_ID;
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $aplyTo)
                );
            }
        }

        $this->configuratorOptionSetupFactory->create(['setup' => $setup])->installEntities();
    }
}