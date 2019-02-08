<?php
/**
 * Created by andrew.
 * Date: 27.11.18
 * Time: 16:04
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\CollectionFactory;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\Collection
    as GroupCollection;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionsProcessor
{
    /** @var ProductConfiguratorOptionRepositoryInterface */
    private $optionRepository;

    /** @var ProductConfiguratorOptionInterfaceFactory  */
    private $optionFactory;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /**
     * ProductConfiguratorOptionsProcessor constructor.
     * @param ProductConfiguratorOptionRepositoryInterface $optionRepository
     * @param ProductConfiguratorOptionInterfaceFactory $optionFactory
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductConfiguratorOptionRepositoryInterface $optionRepository,
        ProductConfiguratorOptionInterfaceFactory $optionFactory,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->optionRepository     = $optionRepository;
        $this->optionFactory        = $optionFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->logger               = $logger;
    }

    /**
     * @param $product ProductInterface
     * @param $groups GroupCollection
     * @param $originalOptions array
     * @return Collection | null
     */
    public function process($product, $groups, $originalOptions)
    {
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $this->deleteOptions($originalOptions, $group);
            }
            return $this->saveOptions($product, $groups);
        }
        return null;
    }

    /**
     * @param ProductConfiguratorOptionInterface[] $originalOptions
     * @param ProductConfiguratorOptionsGroup $group
     */
    private function deleteOptions($originalOptions, $group)
    {
        $assignedIds = [];
        $groupId = $group->getId();
        $assignedOptions = $group->getData('assigned_configurator_options');
        if (!empty($assignedOptions)) {
            foreach ($assignedOptions as $option) {
                if (!empty($option['option_id'])) {
                    $assignedIds[] = $option['option_id'];
                }
            }
        }
        if (empty($originalOptions[$groupId])) {
            return;
        }
        /** @var ProductConfiguratorOptionInterface $originalOption */
        foreach ($originalOptions[$groupId]['options'] as $originalOption) {
            $optionId = $originalOption->getId();
            if (false === array_search($optionId, $assignedIds)) {
                try {
                    $this->optionRepository->deleteById($optionId);
                } catch (CouldNotDeleteException $exception) {
                    $this->logger->error($exception->getMessage());
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param $product ProductInterface | Product
     * @param $groups GroupCollection
     * @return Collection
     */
    private function saveOptions($product, $groups)
    {
        $collection = $this->collectionFactory
            ->create()
            ->addFieldToFilter('product_id', $product->getId());
        /** @var ProductConfiguratorOptionsGroupInterface $group */
        foreach ($groups as $group) {
            if (empty($group->getData('assigned_configurator_options'))) {
                continue;
            }
            foreach ($group->getData('assigned_configurator_options') as $option) {
                if (!is_array($option) || empty($option['configurator_option_id'])) {
                    continue;
                }
                $parentOption = (!empty($option['parent_option'])) ? array_filter($option['parent_option']) : null;
                $dependencies = (!empty($option['dependencies'])) ? $option['dependencies'] : [];
                if ($option['option_id'] && !$product->getData('is_duplicate')) {
                    /** @var ProductConfiguratorOptionInterface $productOption */
                    $productOption = $collection->getItemById($option['option_id']);
                    $productOption->setData($option)
                        ->setParentOption(implode(',', $parentOption))
                        ->setDependencies($dependencies);
                } else {
                    $optionEntity = $this->optionFactory->create();
                    $optionEntity->setData($option)->setGroupId($group->getId());
                    if ($product->getData('is_duplicate')) {
                        $collection->removeItemByKey($option['option_id']);
                    }
                    if (!$option['option_id'] || $product->getData('is_duplicate')) {
                        $optionEntity->setId(null);
                    }
                    $optionEntity->setProductId($product->getId())
                        ->setParentOption($parentOption)
                        ->setDependencies($dependencies);
                    try {
                        $collection->addItem($optionEntity);
                    } catch (\Exception $exception) {
                        $this->logger->error($exception->getMessage());
                    }
                }
            }
        }
        $collection->walk('save');
        return $collection;
    }
}
