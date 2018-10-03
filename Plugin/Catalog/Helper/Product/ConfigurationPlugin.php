<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.10.18
 * Time: 14:46
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Helper\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\StripTags;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class ConfigurationPlugin
{
    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ConfiguratorOptionVariantRepositoryInterface  */
    private $optionVariantRepository;

    /** @var StripTags  */
    private $filter;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ConfiguratorOptionVariantRepositoryInterface $configuratorOptionVariantRepository,
        StripTags $filter,
        LoggerInterface $logger
    ) {
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->optionVariantRepository      = $configuratorOptionVariantRepository;
        $this->filter                       = $filter;
        $this->logger                       = $logger;
    }

    /**
     * Retrieve configuration options for configurator product
     *
     * @param \Magento\Catalog\Helper\Product\Configuration $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetCustomOptions(
        \Magento\Catalog\Helper\Product\Configuration $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
    ) {
        $optionIds = $item->getOptionByCode('configurator_option_ids');
        $product = $item->getProduct();
        $options = [];
        if ($product->getTypeId() != Configurator::TYPE_ID) {
            return $proceed($item);
        }
        foreach (explode(',', $optionIds->getValue()) as $optionId) {
            try {
                $option = $this->configuratorOptionRepository->get($optionId);
            } catch (NoSuchEntityException $exception) {
                $this->logger->error($exception->getMessage());
                continue;
            }
            if ($option && $option->getData('is_visible')) {
                $itemOption = $item->getOptionByCode('configurator_option_' . $option->getId());
                if (in_array($option->getType(), $option->getTypesWithVariants())) {
                    try {
                        $value = $this->optionVariantRepository->get($itemOption->getValue())->getTitle();
                    } catch (NoSuchEntityException $exception) {
                        $this->logger->error($exception->getMessage());
                        continue;
                    }
                } else {
                    $value = $itemOption->getValue();
                }
                $options[] = [
                    'label' => $this->filter->filter($option->getName()),
                    'value' => $value,
                    'print_value' => $value,
                    'option_id' => $option->getId(),
                    'option_type' => $option->getType(),
                    'custom_view' => false,
                ];
            }
        }
        return array_merge($options, $proceed($item));
    }
}
