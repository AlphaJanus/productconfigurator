<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 15:49
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionVariantInterface;

class ProductConfiguratorOptionVariant extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            'catalog_product_configurator_options_variants',
            ProductConfiguratorOptionVariantInterface::VARIANT_ID
        );
    }
}
