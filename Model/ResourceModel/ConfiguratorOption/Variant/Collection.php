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
}
