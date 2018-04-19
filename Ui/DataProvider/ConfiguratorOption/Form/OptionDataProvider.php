<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 16:29
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form;

use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class OptionDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->pool = $pool;
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
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getData()
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
