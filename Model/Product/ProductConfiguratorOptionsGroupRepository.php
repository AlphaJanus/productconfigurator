<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 16:20
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupSearchResultInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupSearchResultInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup as GroupResource;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\CollectionFactory;

class ProductConfiguratorOptionsGroupRepository implements ProductConfiguratorOptionsGroupRepositoryInterface
{
    /** @var GroupResource  */
    private $resourceModel;

    /** @var ProductConfiguratorOptionsGroupInterfaceFactory  */
    private $optionsGroupInterfaceFactory;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /** @var CollectionProcessorInterface  */
    private $collectionProcessor;

    /** @var ProductConfiguratorOptionsGroupSearchResultInterfaceFactory  */
    private $searchResultInterfaceFactory;

    /**
     * ProductConfiguratorOptionsGroupRepository constructor.
     * @param GroupResource $resourceModel
     * @param ProductConfiguratorOptionsGroupInterfaceFactory $optionsGroupInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ProductConfiguratorOptionsGroupSearchResultInterfaceFactory $searchResultInterfaceFactory
     */
    public function __construct(
        GroupResource $resourceModel,
        ProductConfiguratorOptionsGroupInterfaceFactory $optionsGroupInterfaceFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProductConfiguratorOptionsGroupSearchResultInterfaceFactory $searchResultInterfaceFactory
    ) {
        $this->resourceModel                = $resourceModel;
        $this->optionsGroupInterfaceFactory = $optionsGroupInterfaceFactory;
        $this->collectionFactory            = $collectionFactory;
        $this->collectionProcessor          = $collectionProcessor;
        $this->searchResultInterfaceFactory = $searchResultInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductConfiguratorOptionsGroupInterface $optionsGroup)
    {
        try {
            $this->resourceModel->save($optionsGroup);
        } catch (AlreadyExistsException $exception) {
            throw new CouldNotSaveException(__("Could not save option group: %1", $exception->getMessage()));
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__("Could not save option group: %1", $exception->getMessage()));
        }
    }

    public function massSave($groupsData)
    {
        $connection = $this->resourceModel->getConnection();
        try {
            $table = $this->resourceModel->getMainTable();
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(__("Could not save option groups: %1", $exception->getMessage()));
        }
        $fields = $this->resourceModel->getConnection()->describeTable($table);
        $insertData = [];
        $row = [];
        foreach ($groupsData as $data) {
            foreach ($fields as $field => $fieldData) {
                $row[$field] = (!empty($data[$field])) ? $data[$field] : null;
            }
            $insertData[] = $row;
        }
        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($table, $insertData, [$this->resourceModel->getIdFieldName()]);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            throw new CouldNotSaveException(__("Could not save option groups: %1", $exception->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $optionsGroup = $this->optionsGroupInterfaceFactory->create();
        $this->resourceModel->load($optionsGroup, $id);

        if (!$optionsGroup->getId()) {
            throw new NoSuchEntityException(__('There is no option group with id %1', $id));
        }
        return $optionsGroup;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProductConfiguratorOptionsGroupSearchResultInterface $searchResults */
        $searchResults = $this->searchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductConfiguratorOptionsGroupInterface $optionsGroup)
    {
        try {
            $this->resourceModel->delete($optionsGroup);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete option group: %1', $exception->getMessage()));
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
