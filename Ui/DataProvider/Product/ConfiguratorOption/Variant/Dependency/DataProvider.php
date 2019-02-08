<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.01.19
 * Time: 14:28
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\Product\ConfiguratorOption\Variant\Dependency;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\CollectionFactory;

class DataProvider extends AbstractDataProvider
{

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        return parent::getData();
    }
}
