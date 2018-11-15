<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 12:56
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsGroup;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            ProductConfiguratorOptionsGroup::class,
            \Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup::class
        );
    }
}
