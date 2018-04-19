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

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
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
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
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
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
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
                $setup->endSetup();
            }
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
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
    }

}