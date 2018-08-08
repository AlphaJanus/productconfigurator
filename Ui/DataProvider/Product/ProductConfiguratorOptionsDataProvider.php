<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 15.06.18
 * Time: 11:53
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\Product;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\CollectionFactory;

class ProductConfiguratorOptionsDataProvider extends AbstractDataProvider
{
    /** @var CollectionFactory  */
    private $collectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }
        return $this->collection;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        /** @var \Netzexpert\ProductConfigurator\Model\ConfiguratorOption $item */
        foreach ($this->getCollection() as &$option) {
            $values = $option->getVariants()->toArray();
            foreach ($values['items'] as &$item) {
                $item['enabled'] = "1";
            }
            $option->setValues($values['items']);
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
