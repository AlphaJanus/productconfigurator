<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 12:14
 */

namespace Netzexpert\ProductConfigurator\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\StripTags;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;

class Configurator extends AbstractType
{

    const TYPE_ID = 'configurator';

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $productConfiguratorOptionRepository;

    /** @var ConfiguratorOptionVariantRepositoryInterface  */
    private $optionVariantRepository;

    /** @var StripTags  */
    private $filter;

    /**
     * Configurator constructor.
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param ConfiguratorOptionVariantRepositoryInterface $optionVariantRepository
     * @param StripTags $filter
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        ConfiguratorOptionVariantRepositoryInterface $optionVariantRepository,
        StripTags $filter,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->configuratorOptionRepository         = $configuratorOptionRepository;
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->optionVariantRepository              = $optionVariantRepository;
        $this->filter                               = $filter;
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $serializer
        );
    }

    /**
     * Delete data specific for Configurator product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);

        if ($product->getData('configurator_option_groups')) {
            $product->setTypeHasOptions(true);
            $product->setTypeHasRequiredOptions(true);
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        if (!empty($options['info_buyRequest']['configurator_options'])) {
            foreach ($options['info_buyRequest']['configurator_options'] as $optionId => $optionValue) {
                try {
                    $option = $this->productConfiguratorOptionRepository->get($optionId);
                    $configuratorOption = $this->configuratorOptionRepository->get($option->getConfiguratorOptionId());
                } catch (NoSuchEntityException $exception) {
                    $this->_logger->error($exception->getMessage());
                    continue;
                }

                if ($configuratorOption && $configuratorOption->getData('is_visible')) {
                    if (in_array($configuratorOption->getType(), $configuratorOption->getTypesWithVariants())) {
                        try {
                            $value = $this->optionVariantRepository->get($optionValue)->getTitle();
                        } catch (NoSuchEntityException $exception) {
                            $this->_logger->error($exception->getMessage());
                            continue;
                        }
                    } else {
                        $value = $optionValue;
                    }
                    $options['configurator_options'][] = [
                        'label' => $this->filter->filter($configuratorOption->getName()),
                        'value' => $value,
                        'print_value' => $value,
                        'option_id' => $option->getId(),
                        'option_type' => $configuratorOption->getType(),
                        'custom_view' => false,
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * Prepare selected options for product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\DataObject $buyRequest
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processBuyRequest($product, $buyRequest)
    {
        /* add product custom options data */
        $configuratorOptions = $buyRequest->getData('configurator_options');
        if (is_array($configuratorOptions)) {
            array_filter(
                $configuratorOptions,
                function ($value) {
                    return $value !== "";
                }
            );
            return ['configurator_options' => $configuratorOptions];
        }
        return [];
    }
}
