<?php

namespace Netzexpert\ProductConfigurator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Function install
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        try {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('configurator_option_entity')
            );
            $table->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true
                ],
                'Entity id'
            );
            $setup->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $exception) {

        }
        $setup->endSetup();
    }
}
