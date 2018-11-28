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

    /**
     * @param $id int
     * @param $data array
     * @return $this
     */
    public function setItemData($id, $data)
    {
        $this->load();
        if (isset($this->_items[$id])) {
            $this->_items[$id]->setData($data);
        }
        return $this;
    }
}
