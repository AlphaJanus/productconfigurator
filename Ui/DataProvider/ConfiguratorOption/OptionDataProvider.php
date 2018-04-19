<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 12:54
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\CollectionFactory;

class OptionDataProvider extends AbstractDataProvider
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
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
