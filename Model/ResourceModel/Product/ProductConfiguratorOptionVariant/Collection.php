<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 16:01
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionVariant;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant as VariantResource;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            ProductConfiguratorOptionVariant::class,
            VariantResource::class
        );
    }

    public function joinEntityVariantsData()
    {
        return $this->join(
            ['coev' => $this->getTable('configurator_option_entity_variants')],
            'main_table.value_id = coev.value_id'
        );
    }
}
