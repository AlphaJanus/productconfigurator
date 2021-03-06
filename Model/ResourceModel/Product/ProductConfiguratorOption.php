<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 12:34
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;

class ProductConfiguratorOption extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog_product_configurator_options', ProductConfiguratorOptionInterface::ID);
    }
}
