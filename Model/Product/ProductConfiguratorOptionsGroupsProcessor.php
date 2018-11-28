<?php
/**
 * Created by andrew.
 * Date: 27.11.18
 * Time: 13:04
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\CollectionFactory;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionsGroupsProcessor
{
    /** @var ProductConfiguratorOptionsGroupRepositoryInterface  */
    private $groupRepository;

    /** @var ProductConfiguratorOptionsGroupInterfaceFactory */
    private $groupFactory;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ProductConfiguratorOptionsGroupsProcessor constructor.
     * @param ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository
     * @param ProductConfiguratorOptionsGroupInterfaceFactory $groupInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository,
        ProductConfiguratorOptionsGroupInterfaceFactory $groupInterfaceFactory,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->groupRepository      = $groupRepository;
        $this->groupFactory         = $groupInterfaceFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->logger               = $logger;
    }

    /**
     * @param $product ProductInterface
     * @param $options_groups array
     * @param $originalGroups array
     * @return Collection | null
     */
    public function process($product, $options_groups, $originalGroups)
    {
        if (!empty($options_groups)) {
            $this->deleteGroups($originalGroups, $options_groups);
            return $this->saveGroups($product, $options_groups);
        } else {
            $this->deleteGroups($originalGroups, []);
        }
        return null;
    }

    /**
     * @param ProductConfiguratorOptionsGroupInterface[] $originalGroups
     * @param array $options_groups
     */
    private function deleteGroups($originalGroups, $options_groups)
    {
        $assignedGroupsIds = array_column($options_groups, 'group_id');
        foreach ($originalGroups as $originalGroup) {
            $groupId = $originalGroup->getId();
            if (false === array_search($groupId, $assignedGroupsIds)) {
                try {
                    $this->groupRepository->deleteById($groupId);
                } catch (CouldNotDeleteException $exception) {
                    $this->logger->error($exception->getMessage());
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param $product ProductInterface
     * @param $options_groups array
     * @param $originalOptions array
     * @return Collection
     */
    private function saveGroups($product, $options_groups)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory
            ->create()
            ->addFieldToFilter('product_id', $product->getId());
        foreach ($options_groups as &$options_group) {
            $options_group['product_id'] = $product->getId();
            if ($options_group['group_id']) {
                $collection->getItemById($options_group['group_id'])->setData($options_group);
            } else {
                unset($options_group['group_id']);
                $group = $this->groupFactory->create()->setData($options_group);
                try {
                    $collection->addItem($group);
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
        $collection->walk('save');
        return $collection;
    }
}
