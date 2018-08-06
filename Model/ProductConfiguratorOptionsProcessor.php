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
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
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

    /** @var Json  */
    private $json;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ProductConfiguratorOptionsProcessor constructor.
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param ProductConfiguratorOptionInterfaceFactory $productConfiguratorOptionFactory
     * @param ProductExtensionFactory $productExtensionFactory
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        ProductConfiguratorOptionInterfaceFactory $productConfiguratorOptionFactory,
        ProductExtensionFactory $productExtensionFactory,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->productConfiguratorOptionFactory     = $productConfiguratorOptionFactory;
        $this->productExtensionFactory              = $productExtensionFactory;
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
            if (!$originalOptions) {
                $originalOptions = [];
            }
            $assigned_options = $product->getData('assigned_configurator_options');
            if (!empty($assigned_options)) {
                $this->processDeleteOptions($originalOptions, $assigned_options);
                foreach ($assigned_options as $option) {
                    if ($option['option_id']) {
                        try {
                            $optionEntity = $this->productConfiguratorOptionRepository->get($option['option_id']);
                        } catch (NoSuchEntityException $exception) {
                            $this->logger->error($exception->getMessage());
                        }
                    } else {
                        $optionEntity = $this->productConfiguratorOptionFactory->create();
                    }
                    $optionEntity->setData($option);
                    if(!empty($option['values'])) {
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
                $this->deleteAllOptions($originalOptions);
            }
        }
        return $product;
    }

    /**
     * @param ProductConfiguratorOptionInterface[] $options
     */
    private function deleteAllOptions($options)
    {
        foreach ($options as $option) {
            try {
                $this->productConfiguratorOptionRepository->deleteById($option->getId());
            } catch (CouldNotDeleteException $exception) {
                $this->logger->error($exception->getMessage());
            } catch (NoSuchEntityException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * @param ProductConfiguratorOptionInterface[] $originalOptions
     * @param array $assigned_options
     */
    private function processDeleteOptions($originalOptions, $assigned_options)
    {
        $assignedIds = array_column($assigned_options, 'option_id');
        foreach ($originalOptions as $originalOption) {
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
}
