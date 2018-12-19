<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 17.12.18
 * Time: 13:39
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorItemOptionValueInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorItemOptionValueInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Psr\Log\LoggerInterface;

class ProductOptionProcessor implements ProductOptionProcessorInterface
{
    /** @var DataObjectFactory  */
    private $objectFactory;

    /** @var ConfiguratorItemOptionValueInterfaceFactory  */
    private $optionValueInterfaceFactory;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $optionRepository;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $productConfiguratorOptionRepository;

    /** @var ConfiguratorOptionVariantRepositoryInterface  */
    private $optionVariantRepository;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ProductOptionProcessor constructor.
     * @param DataObjectFactory $objectFactory
     * @param ConfiguratorItemOptionValueInterfaceFactory $optionValueInterfaceFactory
     * @param ConfiguratorOptionRepositoryInterface $optionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param ConfiguratorOptionVariantRepositoryInterface $optionVariantRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        ConfiguratorItemOptionValueInterfaceFactory $optionValueInterfaceFactory,
        ConfiguratorOptionRepositoryInterface $optionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        ConfiguratorOptionVariantRepositoryInterface $optionVariantRepository,
        LoggerInterface $logger
    ) {
        $this->objectFactory                        = $objectFactory;
        $this->optionValueInterfaceFactory          = $optionValueInterfaceFactory;
        $this->optionRepository                     = $optionRepository;
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->optionVariantRepository              = $optionVariantRepository;
        $this->logger                               = $logger;
    }

    /**
     * @inheritDoc
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        $this->logger->info(get_class($productOption));
        $object = $this->objectFactory->create();

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function convertToProductOption(DataObject $request)
    {
        $options = $request->getData('configurator_options');
        if (!empty($options) && is_array($options)) {
            $data = [];
            foreach ($options as $optionId => $optionValue) {
                // @codingStandardsIgnoreStart
                /*if (is_array($optionValue)) {
                    $optionValue = $this->processFileOptionValue($optionValue);
                    $optionValue = implode(',', $optionValue);
                }*/ //toDo Implement file upload functionality
                // @codingStandardsIgnoreEnd

                /** @var ConfiguratorItemOptionValueInterface $option */
                $option = $this->optionValueInterfaceFactory->create();
                try {
                    $productOption = $this->productConfiguratorOptionRepository->get($optionId);
                    $configuratorOption = $this->optionRepository->get($productOption->getConfiguratorOptionId());
                    $option->setOptionId($optionId)->setOptionTitle($configuratorOption->getName());
                    if ($configuratorOption->hasVariants()) {
                        $variant = $this->optionVariantRepository->get($optionValue);
                        $option->setOptionValue($variant->getTitle());
                    } else {
                        $option->setOptionValue($optionValue);
                    }
                } catch (NoSuchEntityException $exception) {
                    $this->logger->error($exception->getMessage());
                    $option->setOptionId($optionId)->setOptionValue($optionValue);
                }
                $data[] = $option;
            }
            return ['configurator_options' => $data];
        }
        return [];
    }
}
