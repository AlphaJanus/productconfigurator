<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 11.07.18
 * Time: 9:45
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionsProcessor
{
    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $productConfiguratorOptionRepository;

    /** @var ProductConfiguratorOptionInterfaceFactory  */
    private $productConfiguratorOptionFactory;

    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var ProductConfiguratorOptionsGroupRepositoryInterface  */
    private $groupRepository;

    /** @var ProductConfiguratorOptionsGroupInterfaceFactory  */
    private $groupFactory;

    /** @var Json  */
    private $json;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ProductConfiguratorOptionsProcessor constructor.
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param ProductConfiguratorOptionInterfaceFactory $productConfiguratorOptionFactory
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository
     * @param ProductConfiguratorOptionsGroupInterfaceFactory $groupInterfaceFactory
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        ProductConfiguratorOptionInterfaceFactory $productConfiguratorOptionFactory,
        ProductExtensionFactory $productExtensionFactory,
        ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository,
        ProductConfiguratorOptionsGroupInterfaceFactory $groupInterfaceFactory,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->productConfiguratorOptionFactory     = $productConfiguratorOptionFactory;
        $this->productExtensionFactory              = $productExtensionFactory;
        $this->groupRepository                      = $groupRepository;
        $this->groupFactory                         = $groupInterfaceFactory;
        $this->json                                 = $json;
        $this->logger                               = $logger;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function process($product)
    {
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $extensionAttributes = $product->getExtensionAttributes();
            $productExtension = $extensionAttributes ?
                $extensionAttributes : $this->productExtensionFactory->create();
            $originalOptions = $productExtension->getConfiguratorOptions();
            $originalGroups = $productExtension->getConfiguratorOptionsGroups();
            if (!$originalOptions) {
                $originalOptions = [];
            }
            if (!$originalGroups) {
                $originalGroups = [];
            }
            $options_groups = $product->getData('configurator_option_groups');
            if (!empty($options_groups)) {
                $this->processDeleteGroups($originalGroups, $options_groups);
                foreach ($options_groups as $options_group) {
                    if ($options_group['group_id']) {
                        try {
                            $group = $this->groupRepository->get($options_group['group_id']);
                        } catch (NoSuchEntityException $exception) {
                            $this->logger->error($exception->getMessage());
                        }
                    } else {
                        $group = $this->groupFactory->create();
                        unset($options_group['group_id']);
                    }
                    $group->setData($options_group)->setProductId($product->getId());
                    try {
                        $this->groupRepository->save($group);
                    } catch (CouldNotSaveException $exception) {
                        $this->logger->error($exception->getMessage());
                    }

                    if (!empty($group->getData('assigned_configurator_options'))) {
                        $this->processDeleteOptions($originalOptions, $group);
                        foreach ($group['assigned_configurator_options'] as $option) {
                            if ($option['option_id']) {
                                try {
                                    $optionEntity = $this->productConfiguratorOptionRepository
                                                        ->get($option['option_id']);
                                } catch (NoSuchEntityException $exception) {
                                    $this->logger->error($exception->getMessage());
                                }
                            } else {
                                $optionEntity = $this->productConfiguratorOptionFactory->create();
                            }
                            $optionEntity->setData($option)->setGroupId($group->getId());
                            if (!empty($option['values'])) {
                                $optionEntity->setValuesData($this->json->serialize($option['values']));
                            }
                            if (!$option['option_id']) {
                                $optionEntity->setId(null);
                            }
                            $optionEntity->setProductId($product->getId());
                            try {
                                $this->productConfiguratorOptionRepository->save($optionEntity);
                            } catch (CouldNotSaveException $exception) {
                                $this->logger->error($exception->getMessage());
                            }
                        }
                    } else {
                        $this->deleteAllOptions($originalOptions, $group);
                    }
                }
            } else {
                $this->processDeleteGroups($originalGroups, []);
            }
        }
        return $product;
    }

    /**
     * @param ProductConfiguratorOptionInterface[] $options
     * @param ProductConfiguratorOptionsGroupInterface $group
     */
    private function deleteAllOptions($options, $group)
    {
        if (!empty($options[$group->getId()]['options'])) {
            foreach ($options[$group->getId()]['options'] as $option) {
                try {
                    $this->productConfiguratorOptionRepository->deleteById($option->getId());
                } catch (CouldNotDeleteException $exception) {
                    $this->logger->error($exception->getMessage());
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param ProductConfiguratorOptionInterface[] $originalOptions
     * @param ProductConfiguratorOptionsGroupInterface $group
     */
    private function processDeleteOptions($originalOptions, $group)
    {
        $assignedIds = array_column($group->getData('assigned_configurator_options'), 'option_id');
        foreach ($originalOptions[$group->getId()]['options'] as $originalOption) {
            $optionId = $originalOption->getId();
            if (false === array_search($optionId, $assignedIds)) {
                try {
                    $this->productConfiguratorOptionRepository->deleteById($optionId);
                } catch (CouldNotDeleteException $exception) {
                    $this->logger->error($exception->getMessage());
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param ProductConfiguratorOptionsGroupInterface[] $originalGroups
     * @param array $options_groups
     */
    private function processDeleteGroups($originalGroups, $options_groups)
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
}
