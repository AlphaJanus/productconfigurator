<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 12:10
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption
    as ProductConfiguratorOptionResource;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class ProductConfiguratorOptionRepository implements ProductConfiguratorOptionRepositoryInterface
{
    /** @var ProductConfiguratorOptionResource  */
    private $resourceModel;

    /** @var Data\ProductConfiguratorOptionInterfaceFactory  */
    private $productConfiguratorOptionInterfaceFactory;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /** @var CollectionProcessorInterface  */
    private $collectionProcessor;

    /** @var Data\ProductConfiguratorOptionSearchResultInterfaceFactory  */
    private $searchResultsFactory;

    public function __construct(
        ProductConfiguratorOptionResource $productConfiguratorOptionResource,
        Data\ProductConfiguratorOptionInterfaceFactory $productConfiguratorOptionInterfaceFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\ProductConfiguratorOptionSearchResultInterfaceFactory $searchResultInterfaceFactory
    ) {
        $this->resourceModel                                = $productConfiguratorOptionResource;
        $this->productConfiguratorOptionInterfaceFactory    = $productConfiguratorOptionInterfaceFactory;
        $this->collectionFactory                            = $collectionFactory;
        $this->collectionProcessor                          = $collectionProcessor;
        $this->searchResultsFactory                          = $searchResultInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductConfiguratorOptionInterface $productConfiguratorOption)
    {
        try {
            $this->resourceModel->save($productConfiguratorOption);
        } catch (AlreadyExistsException $exception) {
            throw new CouldNotSaveException(__("Could not save option link: %1", $exception->getMessage()));
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__("Could not save option link: %1", $exception->getMessage()));
        }
        return $productConfiguratorOption;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $productConfiguratorOption = $this->productConfiguratorOptionInterfaceFactory->create();
        $this->resourceModel->load($productConfiguratorOption, $id);
        if (!$productConfiguratorOption->getId()) {
            throw new NoSuchEntityException(__('There is no option assiged under id %1', $id));
        }
        return $productConfiguratorOption;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var Data\ProductConfiguratorOptionSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductConfiguratorOptionInterface $productConfiguratorOption)
    {
        try {
            $this->resourceModel->delete($productConfiguratorOption);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete option link: %1', $exception->getMessage()));
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
