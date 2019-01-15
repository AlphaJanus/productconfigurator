<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 17.07.18
 * Time: 13:30
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterfaceFactory;
use Psr\Log\LoggerInterface;

class ConfiguratorOptionVariantsProcessor
{
    /** @var ConfiguratorOptionVariantRepositoryInterface  */
    private $variantRepository;

    /** @var ConfiguratorOptionVariantInterfaceFactory  */
    private $variantFactory;

    /** @var ManagerInterface  */
    private $messageManager;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(
        ConfiguratorOptionVariantRepositoryInterface $variantRepository,
        ConfiguratorOptionVariantInterfaceFactory $configuratorOptionVariantInterfaceFactory,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->variantRepository    = $variantRepository;
        $this->variantFactory       = $configuratorOptionVariantInterfaceFactory;
        $this->messageManager       = $messageManager;
        $this->logger               = $logger;
    }

    /**
     * @param ConfiguratorOptionInterface $option
     */
    public function processVariants($option)
    {
        if (in_array($option->getType(), $option->getTypesWithVariants())) {
            $this->saveVariants($option);
        } else {
            $this->deleteVariants($option);
        }
    }

    /**
     * @param ConfiguratorOptionInterface $option
     */
    private function saveVariants($option)
    {
        foreach ($option->getValues() as $value) {
            if ($value['value_id'] && !$option->isDuplicate()) {
                try {
                    $variant = $this->variantRepository->get($value['value_id']);
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            } else {
                unset($value['value_id']);
                $variant = $this->variantFactory->create();
            }
            try {
                $variant->setData($value);
                if (isset($value['image'])) {
                    $variant->setImage($value["image"][0]["file"]);
                } else {
                    $variant->setImage(null);
                }
                $variant->setConfiguratorOptionId($option->getId());
                $this->variantRepository->save($variant);
            } catch (CouldNotSaveException $exception) {
                $this->messageManager->addExceptionMessage($exception);
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * @param ConfiguratorOptionInterface $option
     */
    private function deleteVariants($option)
    {
        foreach ($option->getValues() as $value) {
            if ($value['value_id']) {
                try {
                    $this->variantRepository->deleteById($value['value_id']);
                } catch (CouldNotDeleteException $exception) {
                    $this->logger->error($exception->getMessage());
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }
}
