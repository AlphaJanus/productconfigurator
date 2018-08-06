<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 13:58
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            ProductConfiguratorOption::class,
            \Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption::class
        );
    }

}
