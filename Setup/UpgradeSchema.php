<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 17.04.18
 * Time: 20:43
 */

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Psr\Log\LoggerInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * UpgradeSchema constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.22') < 0) {
            $this->upgradeVersionTwoZeroTwentyTwo($setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroTwentyTwo(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.20') < 0) {
            $this->upgradeVersionTwoZeroTwenty($setup, $context);
        }
        $setup->getConnection()->dropIndex(
            $setup->getTable('configurator_option_entity_variants'),
            $setup->getIdxName(
                'configurator_option_entity_variants',
                ['option_id', 'is_default'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroTwenty(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.19') < 0) {
            $this->upgradeVersionTwoZeroNineteen($setup, $context);
        }
        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_entity_variants'),
            'show_in_cart',
            [
                'type'      => Table::TYPE_SMALLINT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => 1,
                'comment'   => 'Show in cart'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroNineteen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.18') < 0) {
            $this->upgradeVersionTwoZeroEighteen($setup, $context);
        }

        $setup->getConnection()->changeColumn(
            $setup->getTable('catalog_product_configurator_options_variants'),
            'allowed_variants',
            'dependencies',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Dependencies'
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable('catalog_product_configurator_options'),
            'allowed_variants',
            'dependencies',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Dependencies'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroEighteen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.16') < 0) {
            $this->upgradeVersionTwoZeroSixteen($setup, $context);
        }

        $setup->getConnection()->dropIndex(
            $setup->getTable('catalog_product_configurator_options'),
            $setup->getIdxName('catalog_product_configurator_options', ['parent_option'])
        );
        $setup->getConnection()->changeColumn(
            $setup->getTable('catalog_product_configurator_options'),
            'parent_option',
            'parent_option',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Parent option'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeVersionTwoZeroSixteen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.15') < 0) {
            $this->upgradeVersionTwoZeroFifteen($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('catalog_product_configurator_options'),
            'allowed_variants',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Enabled on parent option variants'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroFifteen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.13') < 0) {
            $this->upgradeVersionTwoZeroThirteen($setup, $context);
        }

        try {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('catalog_product_configurator_options_variants')
            );
            $table->addColumn(
                'variant_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Record id'
            )->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true
                ],
                'Product configurator option id'
            )->addColumn(
                'configurator_option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true
                ],
                'Configurator option entity id'
            )->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true
                ],
                'Configurator option variant id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true
                ],
                'Product entity id'
            )->addColumn(
                'enabled',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => 0
                ],
                'Is variant enabled for this product'
            )->addColumn(
                'is_dependent',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => 0
                ],
                'Is variant dependent from parent option'
            )->addColumn(
                'allowed_variants',
                Table::TYPE_TEXT,
                '64k',
                [],
                'This variant is allowed for next variants of parent option'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['option_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['option_id']
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['configurator_option_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['configurator_option_id']
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['value_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['value_id']
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['product_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['product_id']
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['enabled'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['enabled']
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    ['is_dependent'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['is_dependent']
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    'option_id',
                    $setup->getTable('catalog_product_configurator_options'),
                    'option_id'
                ),
                'option_id',
                $setup->getTable('catalog_product_configurator_options'),
                'option_id',
                AdapterInterface::FK_ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    'configurator_option_id',
                    $setup->getTable('configurator_option_entity'),
                    'entity_id'
                ),
                'configurator_option_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                AdapterInterface::FK_ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    'value_id',
                    $setup->getTable('configurator_option_entity_variants'),
                    'value_id'
                ),
                'value_id',
                $setup->getTable('configurator_option_entity_variants'),
                'value_id',
                AdapterInterface::FK_ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('catalog_product_configurator_options_variants'),
                    'product_id',
                    $setup->getTable('catalog_product_entity'),
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                AdapterInterface::FK_ACTION_CASCADE
            );
            $setup->getConnection()->createTable($table);
            $setup->getConnection()
                ->dropColumn(
                    $setup->getTable('catalog_product_configurator_options'),
                    'values_data'
                );
            $setup->getConnection()
                ->changeColumn(
                    $setup->getTable('configurator_option_entity_variants'),
                    'option_id',
                    'configurator_option_id',
                    [
                        'type'  => Table::TYPE_INTEGER,
                        'size'  => null,
                        'nullable' => false,
                        'unsigned' => true
                    ]
                );
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroThirteen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.11') < 0) {
            $this->upgradeVersionTwoZeroEleven($setup, $context);
        }

        try {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('catalog_product_configurator_option_groups')
            );
            $table->addColumn(
                'group_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Group id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Product Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Group name'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0
                ],
                'Sort order'
            )->addIndex(
                $setup->getIdxName('catalog_product_configurator_option_groups', ['product_id']),
                ['product_id']
            )->addForeignKey(
                $setup->getFkName(
                    'catalog_product_configurator_option_groups',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
            $setup->getConnection()->createTable($table);

            /** Add group_id column to catalog_product_configurator_options table */
            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_configurator_options'),
                'group_id',
                [
                    'type'      => Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'unsigned'  => true,
                    'comment'   => 'Options group Id',
                    'after'     => 'configurator_option_id'
                ]
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('catalog_product_configurator_options'),
                $setup->getIdxName('catalog_product_configurator_options', ['group_id']),
                ['group_id']
            );
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                    'catalog_product_configurator_options',
                    'group_id',
                    'catalog_product_configurator_option_groups',
                    'group_id'
                ),
                $setup->getTable('catalog_product_configurator_options'),
                'group_id',
                $setup->getTable('catalog_product_configurator_option_groups'),
                'group_id',
                Table::ACTION_CASCADE
            );
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
            $setup->endSetup();
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroEleven(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.10') < 0) {
            $this->upgradeVersionTwoZeroTen($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('catalog_product_configurator_options'),
            'values_data',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Values data'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroTen(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.9') < 0) {
            $this->upgradeVersionTwoZeroNine($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('catalog_product_configurator_options'),
            'parent_option',
            [
                'type'      => Table::TYPE_INTEGER,
                'length'    => null,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Parent option'
            ]
        );
        $setup->getConnection()->addIndex(
            $setup->getTable('catalog_product_configurator_options'),
            $setup->getIdxName('catalog_product_configurator_options', ['parent_option']),
            ['parent_option']
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroNine(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.8') < 0) {
            $this->upgradeVersionTwoZeroEight($setup, $context);
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('catalog_product_configurator_options')
        );
        try {
            $table->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ],
                'Option id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Product Id'
            )->addColumn(
                'configurator_option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Configurator option Id'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0
                ],
                'Sort order'
            )->addIndex(
                $setup->getIdxName(
                    'catalog_product_configurator_options',
                    ['product_id','configurator_option_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_id','configurator_option_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName('catalog_product_configurator_options', ['product_id']),
                ['product_id']
            )->addIndex(
                $setup->getIdxName('catalog_product_configurator_options', ['configurator_option_id']),
                ['configurator_option_id']
            )->addForeignKey(
                $setup->getFkName(
                    'catalog_product_configurator_options',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'catalog_product_configurator_options',
                    'configurator_option_id',
                    'configurator_option_entity',
                    'entity_id'
                ),
                'configurator_option_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
            $setup->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
            $setup->endSetup();
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroEight(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.7') < 0) {
            $this->upgradeVersionTwoZeroSeven($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_entity'),
            'code',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '',
                'comment'   => 'Option Code'
            ]
        );
        $setup->getConnection()->addIndex(
            $setup->getTable('configurator_option_entity'),
            $setup->getIdxName(
                'configurator_option_entity',
                'code',
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            'code',
            AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroSeven(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.6') < 0) {
            $this->upgradeVersionTwoZeroSix($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_entity_variants'),
            'image',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Variant image'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroSix(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.5') < 0) {
            $this->upgradeVersionTwoZeroFive($setup, $context);
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('configurator_option_entity_variants')
        );
        try {
            $table->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ],
                'Variant id'
            )->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Option id'
            )->addColumn(
                'title',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Variant title'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Variant value'
            )->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0
                ],
                'Sort order'
            )->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,4',
                [
                    'nullable' => false,
                    'default' => '0.0000'
                ],
                'Price'
            )->addColumn(
                'is_default',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0
                ],
                'Is value default'
            )->addIndex(
                $setup->getIdxName(
                    'configurator_option_entity_variants',
                    ['option_id', 'is_default'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['option_id', 'is_default'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_variants',
                    'option_id',
                    'configurator_option_entity',
                    'entity_id'
                ),
                'option_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
            $setup->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
            $setup->endSetup();
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroFive(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $this->upgradeVersionTwoZeroThree($setup, $context);
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_eav_attribute'),
            'apply_to',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Field is applicable to'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroThree(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $this->upgradeVersionTwoZeroTwo($setup, $context);
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('configurator_option_eav_attribute')
        );
        try {
            $table->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0'
                ],
                'Attribute id'
            )->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0'
                ],
                'Attribute sort order'
            )->addColumn(
                'is_visible_in_grid',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0'
                ],
                'Is attribute visible in grid'
            )->addColumn(
                'is_filterable_in_grid',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0'
                ],
                'Is filterable visible in grid'
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_eav_attribute',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->setComment('Configurator option eav attribute');
            $setup->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
            $setup->endSetup();
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeVersionTwoZeroTwo(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $this->upgradeVersionTwoZeroOne($setup);
        }
        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_entity'),
            'created_at',
            [
                'type'      => Table::TYPE_TIMESTAMP,
                'length'    => null,
                'nullable'  => false,
                'default'   => Table::TIMESTAMP_INIT,
                'comment'   => 'Creation Time'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('configurator_option_entity'),
            'updated_at',
            [
                'type'      => Table::TYPE_TIMESTAMP,
                'length'    => null,
                'nullable'  => false,
                'default'   => Table::TIMESTAMP_INIT_UPDATE,
                'comment'   => 'Update Time'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeVersionTwoZeroOne(SchemaSetupInterface $setup)
    {
        try {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('configurator_option_entity_int')
            );
            $table->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Value id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Attribute id'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Entity id'
            )->addColumn(
                'value',
                Table::TYPE_INTEGER,
                null,
                [],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('configurator_option_entity_int'),
                    ['entity_id', 'attribute_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName('configurator_option_entity_int', ['attribute_id']),
                ['attribute_id']
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_int',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_int',
                    'entity_id',
                    'configurator_option_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Configurator Option Entity Integer Attribute Backend Table');
            $setup->getConnection()->createTable($table);

            /**-------*/
            $table = $setup->getConnection()->newTable(
                $setup->getTable('configurator_option_entity_text')
            );
            $table->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Value id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Attribute id'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Entity id'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('configurator_option_entity_text'),
                    ['entity_id', 'attribute_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName('configurator_option_entity_text', ['attribute_id']),
                ['attribute_id']
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_text',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_text',
                    'entity_id',
                    'configurator_option_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Configurator Option Entity Text Attribute Backend Table');
            $setup->getConnection()->createTable($table);

            /**-----------*/
            $table = $setup->getConnection()->newTable(
                $setup->getTable('configurator_option_entity_varchar')
            );
            $table->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Value id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Attribute id'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable'  => false,
                    'unsigned'  => true,
                    'default'   => '0'
                ],
                'Entity id'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable'  => true,
                    'default'  => null
                ],
                'Attribute value'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('configurator_option_entity_varchar'),
                    ['entity_id', 'attribute_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName('configurator_option_entity_varchar', ['attribute_id']),
                ['attribute_id']
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_varchar',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'configurator_option_entity_varchar',
                    'entity_id',
                    'configurator_option_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable('configurator_option_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Configurator Option Entity Varchar Attribute Backend Table');
            $setup->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error($exception->getMessage());
            $setup->endSetup();
        }
    }
}
