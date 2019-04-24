<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.04.19
 * Time: 11:30
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsVariantSearchResultInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsVariantSearchResultInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionVariantInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionVariantInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant as VariantResource;

class ProductConfiguratorOptionsVariantRepository implements ProductConfiguratorOptionsVariantRepositoryInterface
{
    /** @var VariantResource  */
    private $variantResource;

    /** @var ProductConfiguratorOptionVariantInterfaceFactory  */
    private $variantInterfaceFactory;

    /** @var VariantResource\CollectionFactory  */
    private $collectionFactory;

    /** @var CollectionProcessorInterface  */
    private $collectionProcessor;

    /** @var ProductConfiguratorOptionsVariantSearchResultInterfaceFactory  */
    private $searchResultInterfaceFactory;

    /**
     * ProductConfiguratorOptionsVariantRepository constructor.
     * @param VariantResource $variantResource
     * @param ProductConfiguratorOptionVariantInterfaceFactory $variantInterfaceFactory
     * @param VariantResource\CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ProductConfiguratorOptionsVariantSearchResultInterfaceFactory $searchResultInterfaceFactory
     */
    public function __construct(
        VariantResource $variantResource,
        ProductConfiguratorOptionVariantInterfaceFactory $variantInterfaceFactory,
        VariantResource\CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProductConfiguratorOptionsVariantSearchResultInterfaceFactory $searchResultInterfaceFactory
    ) {
        $this->variantResource              = $variantResource;
        $this->variantInterfaceFactory      = $variantInterfaceFactory;
        $this->collectionFactory            = $collectionFactory;
        $this->collectionProcessor          = $collectionProcessor;
        $this->searchResultInterfaceFactory = $searchResultInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductConfiguratorOptionVariantInterface $variant)
    {
        try {
            $this->variantResource->save($variant);
        } catch (AlreadyExistsException $exception) {
            throw new CouldNotSaveException(__("Could not save option variant: %1", $exception->getMessage()));
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__("Could not save option variant: %1", $exception->getMessage()));
        }
        return $variant;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $variant = $this->variantInterfaceFactory->create();
        $this->variantResource->load($variant, $id);
        if (!$variant->getId()) {
            throw new NoSuchEntityException(__('There is no variant assigned under id %1', $id));
        }
        return $variant;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductConfiguratorOptionVariantInterface $variant)
    {
        try {
            $this->variantResource->delete($variant);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete option variant link: %1', $exception->getMessage()));
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

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var VariantResource\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProductConfiguratorOptionsVariantSearchResultInterface $searchResult */
        $searchResults = $this->searchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

}
