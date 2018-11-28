<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 04.05.18
 * Time: 10:51
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            'Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Variant',
            '\Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant'
        );
    }

    public function joinProductVariantsData()
    {
        $this->getSelect()->joinLeft(
            ['cpcov' => $this->getTable('catalog_product_configurator_options_variants')],
            'main_table.value_id = cpcov.value_id',
            [
                'cpcov.variant_id',
                'cpcov.configurator_option_id',
                'cpcov.product_id',
                'cpcov.enabled',
                'cpcov.is_dependent',
                'cpcov.allowed_variants'
            ]
        );
        return $this;
    }
}
