<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 12:56
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption;

use \Magento\Eav\Model\Entity\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Netzexpert\ProductConfigurator\Model\ConfiguratorOption::class,
            \Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption::class
        );
    }
}
