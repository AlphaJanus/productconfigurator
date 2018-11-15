<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 12:52
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;

class ProductConfiguratorOptionsGroup extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog_product_configurator_option_groups', ProductConfiguratorOptionsGroupInterface::ID);
    }
}
