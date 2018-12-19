<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.05.18
 * Time: 17:31
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant as VariantResource;

class VariantRepository implements ConfiguratorOptionVariantRepositoryInterface
{

    /** @var VariantResource  */
    private $variantResource;

    /** @var Data\ConfiguratorOptionVariantInterfaceFactory  */
    private $variantFactory;

    /** @var Data\ConfiguratorOptionVariantSearchResultInterfaceFactory  */
    private $searchResultFactory;

    /** @var VariantResource\CollectionFactory  */
    private $collectionFactory;

    /** @var DataObjectHelper  */
    private $dataObjectHelper;

    /** @var DataObjectProcessor  */
    private $dataObjectProcessor;

    public function __construct(
        VariantResource $variantResource,
        Data\ConfiguratorOptionVariantInterfaceFactory $variantInterfaceFactory,
        Data\ConfiguratorOptionVariantSearchResultInterfaceFactory $searchResultFactory,
        VariantResource\CollectionFactory $collectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->variantResource      = $variantResource;
        $this->variantFactory       = $variantInterfaceFactory;
        $this->searchResultFactory  = $searchResultFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->dataObjectProcessor  = $dataObjectProcessor;
    }
    /**
     * @inheritDoc
     */
    public function save(ConfiguratorOptionVariantInterface $variant)
    {
        try {
            $this->variantResource->save($variant);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save variant: %1', $exception->getMessage()));
        }
        return $variant;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $variant = $this->variantFactory->create();
        $this->variantResource->load($variant, $id);
        if (!$variant->getId()) {
            throw new NoSuchEntityException(__('Variant with id %1 does not exists', $id));
        }
        return $variant;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->collectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $variants = [];
        /** @var Variant $variantModel */
        foreach ($collection as $variantModel) {
            $variantData = $this->variantFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $variantData,
                $variantModel->getData(),
                'Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface'
            );
            $variants[] = $this->dataObjectProcessor->buildOutputDataArray(
                $variantData,
                'Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface'
            );
        }
        $searchResults->setItems($variants);
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ConfiguratorOptionVariantInterface $variant)
    {
        try {
            $this->variantResource->delete($variant);
            return true;
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete variant: %1', $exception->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }
}
