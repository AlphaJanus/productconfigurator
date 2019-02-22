<?php
/** @noinspection PhpUnusedParameterInspection */

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 19.09.18
 * Time: 15:58
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\Manager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Validator\Exception;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
use Netzexpert\ProductConfigurator\Model\Product\ConfiguratorOption\FileProcessor;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class AbstractTypePlugin
{
    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var ConfiguratorOptionRepositoryInterface */
    private $configuratorOptionRepository;

    /** @var ConfiguratorOptionVariantRepositoryInterface */
    private $optionVariantRepository;

    /** @var Json  */
    private $serializer;

    /** @var FileProcessor  */
    private $fileProcessor;

    /** @var Manager  */
    private $messageManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * AbstractTypePlugin constructor.
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param Json $serializer
     * @param FileProcessor $fileProcessor
     * @param Manager $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductExtensionFactory $productExtensionFactory,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ConfiguratorOptionVariantRepositoryInterface $optionVariantRepository,
        Json $serializer,
        FileProcessor $fileProcessor,
        Manager $messageManager,
        LoggerInterface $logger
    ) {
        $this->productExtensionFactory      = $productExtensionFactory;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->optionVariantRepository      = $optionVariantRepository;
        $this->serializer                   = $serializer;
        $this->fileProcessor                = $fileProcessor;
        $this->messageManager               = $messageManager;
        $this->logger                       = $logger;
    }

    /**
     * @param AbstractType $abstractType
     * @param DataObject $buyRequest
     * @param Product $product
     * @param null|string $processMode
     * @return array
     */
    public function beforePrepareForCartAdvanced(
        AbstractType $abstractType,
        DataObject $buyRequest,
        $product,
        $processMode
    ) {
        $options = $this->prepareOptions($buyRequest, $product);
        $optionIds = array_keys($options);
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $product->addCustomOption('configurator_option_ids', implode(',', $optionIds));
            foreach ($options as $optionId => $optionValue) {
                $product->addCustomOption(Configurator::CONFIGURATOR_OPTION_PREFIX . $optionId, $optionValue);
            }
        }
        return [$buyRequest, $product, $processMode];
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @return array
     */
    private function prepareOptions($buyRequest, $product)
    {
        $options = [];
        $extensionAttributes = $product->getExtensionAttributes();
        $productExtension = $extensionAttributes ? $extensionAttributes : $this->productExtensionFactory->create();
        $configuratorOptions = $productExtension->getConfiguratorOptions();
        $requestOptions = $buyRequest->getDataByKey('configurator_options');
        if ($configuratorOptions != null && $requestOptions != null) {
            foreach ($configuratorOptions as $optionGroup) {
                /** @var ProductConfiguratorOptionInterface $option */
                foreach ($optionGroup['options'] as $option) {
                    try {
                        $optionEntity = $this->configuratorOptionRepository->get($option->getConfiguratorOptionId());
                    } catch (NoSuchEntityException $exception) {
                        $this->logger->error($exception->getMessage());
                        continue;
                    }
                    $optionValue = (!empty($requestOptions[$option->getId()])) ? $requestOptions[$option->getId()] : '';
                    if ($optionValue) {
                        $showInCart = false;
                        if ($optionEntity->hasVariants()) {
                            try {
                                $variant = $this->optionVariantRepository->get($optionValue);
                                $showInCart = $variant->getShowInCart();
                            } catch (NoSuchEntityException $exception) {
                                $this->logger->error($exception->getMessage());
                            }
                        }
                        if ($showInCart) {
                            $options[$option->getConfiguratorOptionId()] = $optionValue;
                        }
                    }
                    if ($optionEntity->getType() == OptionType::TYPE_FILE) {
                        try {
                            $optionValue = $this->fileProcessor->validate($option, $optionEntity, $product);
                            $options[$option->getConfiguratorOptionId()] = $this->serializer->serialize($optionValue);
                        } catch (Exception $exception) {
                            $this->logger->error($exception->getMessage());
                            $this->messageManager->addExceptionMessage($exception);
                            $options[$option->getConfiguratorOptionId()] = null;
                        } catch (LocalizedException $exception) {
                            $this->logger->error($exception->getMessage());
                            $this->messageManager->addExceptionMessage($exception);
                            $options[$option->getConfiguratorOptionId()] = null;
                        }
                    }
                }
            }
        }
        return $options;
    }
}
