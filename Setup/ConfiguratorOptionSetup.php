<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:14
 */

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption;

class ConfiguratorOptionSetup extends EavSetup
{
    /** @var Config  */
    private $eavConfig;

    /**
     * ConfiguratorOptionSetup constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    public function getDefaultEntities()
    {
        return [
            'configurator_option_entity' => [
                'entity_model'                  => ConfiguratorOption::class,
                'table'                         => 'configurator_option_entity',
                'attributes'    => [
                    'name'  => [
                        'type'  => 'varchar',
                        'label' => 'Name',
                        'input' => 'text'
                    ],
                    'type' => [
                        'type'          => 'varchar',
                        'label'         => 'Type',
                        'input'         => 'select',
                        'source' => \Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType::class,
                    ],
                    'description' => [
                        'type'          => 'text',
                        'label'         => 'Description',
                        'input'         => 'textarea',
                        'is_required'   => false
                    ],
                    'is_required' => [
                        'type'          => 'int',
                        'label'         => 'Is required',
                        'input'         => 'boolean',
                        'source'        => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                        'is_required'   => false
                    ],
                    'is_visible' => [
                        'type'          => 'int',
                        'label'         => 'Is visible',
                        'input'         => 'boolean',
                        'source'        => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                        'is_required'   => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Gets EAV configuration
     *
     * @return Config
     */
    public function getEavConfig()
    {
        return $this->eavConfig;
    }
}
