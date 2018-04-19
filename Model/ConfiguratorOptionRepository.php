<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 14:25
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionSearchResultsInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionSearchResultsInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;

class ConfiguratorOptionRepository implements ConfiguratorOptionRepositoryInterface
{
    /** @var ResourceModel\ConfiguratorOption  */
    private $configuratorOptionResource;

    /** @var ConfiguratorOptionFactory  */
    private $configuratorOptionFactory;

    /** @var ConfiguratorOptionSearchResultsInterfaceFactory  */
    private $searchResultsFactory;

    /** @var CollectionProcessorInterface  */
    private $collectionProcessor;

    /** @var ResourceModel\ConfiguratorOption\CollectionFactory  */
    private $collectionFactory;

    /**
     * ConfiguratorOptionRepository constructor.
     * @param ResourceModel\ConfiguratorOption $configuratorOptionResource
     * @param ConfiguratorOptionFactory $configuratorOptionFactory
     * @param ConfiguratorOptionSearchResultsInterfaceFactory $configuratorOptionSearchResultsInterfaceFactory
     * @param ResourceModel\ConfiguratorOption\CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel\ConfiguratorOption $configuratorOptionResource,
        ConfiguratorOptionFactory $configuratorOptionFactory,
        ConfiguratorOptionSearchResultsInterfaceFactory $configuratorOptionSearchResultsInterfaceFactory,
        ResourceModel\ConfiguratorOption\CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->configuratorOptionResource   = $configuratorOptionResource;
        $this->configuratorOptionFactory    = $configuratorOptionFactory;
        $this->searchResultsFactory         = $configuratorOptionSearchResultsInterfaceFactory;
        $this->collectionFactory            = $collectionFactory;
        $this->collectionProcessor          = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        /** @var ConfiguratorOptionInterface $configuratorOption */
        $configuratorOption = $this->configuratorOptionFactory->create();
        $this->configuratorOptionResource->load($configuratorOption, $id);
        if (!$configuratorOption->getId()) {
            throw new NoSuchEntityException(__('Option with id %1 does not exist', $id));
        }
        return $configuratorOption;
    }

    /**
     * @inheritDoc
     */
    public function save(ConfiguratorOptionInterface $configuratorOption)
    {
        try {
            $this->configuratorOptionResource->save($configuratorOption);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save option: %1', $exception->getMessage()));
        }
        return $configuratorOption;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ConfiguratorOptionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ConfiguratorOptionInterface $configuratorOption)
    {
        try {
            $this->configuratorOptionResource->delete($configuratorOption);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete option: %1', $exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }
}
