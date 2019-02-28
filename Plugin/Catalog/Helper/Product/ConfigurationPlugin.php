<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.10.18
 * Time: 14:46
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Helper\Product;

use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
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

    /** @var Json  */
    private $serializer;

    /** @var UrlInterface  */
    private $urlBuilder;

    /** @var Escaper  */
    private $escaper;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * Url for custom option download controller
     * @var string
     */
    private $configuratorOptionDownloadUrl = 'sales/download/downloadConfiguratorOption';

    public function __construct(
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ConfiguratorOptionVariantRepositoryInterface $configuratorOptionVariantRepository,
        StripTags $filter,
        Json $serializer,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        LoggerInterface $logger
    ) {
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->optionVariantRepository      = $configuratorOptionVariantRepository;
        $this->filter                       = $filter;
        $this->serializer                   = $serializer;
        $this->urlBuilder                   = $urlBuilder;
        $this->escaper                      = $escaper;
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
                $itemOption = $item->getOptionByCode(Configurator::CONFIGURATOR_OPTION_PREFIX . $option->getId());
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
                if ($option->getType() == OptionType::TYPE_FILE && $value == '[]') {
                    continue;
                }
                $options[] = [
                    'label' => $this->filter->filter($option->getName()),
                    'value' => $this->getFormattedOptionValue($option, $value, $item),
                    'print_value' => $value,
                    'option_id' => $option->getId(),
                    'option_type' => $option->getType(),
                    'custom_view' => false,
                ];
            }
        }
        return array_merge($options, $proceed($item));
    }

    /**
     * Return formatted option value for quote option
     *
     * @param $option ConfiguratorOptionInterface
     * @param string $optionValue Prepared for cart option value
     * @param $item \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface
     * @return string
     */
    private function getFormattedOptionValue($option, $optionValue, $item)
    {
        $value = $optionValue;
        if ($option->getType() == OptionType::TYPE_FILE && $optionValue) {
            $value = $this->serializer->unserialize($optionValue);
        }
        if ($value === null || !is_array($value)) {
            return $optionValue;
        }
        if (empty($value)) {
            return '';
        }
        $customOptionUrlParams =  [
            'id' => $item->getOptionByCode(Configurator::CONFIGURATOR_OPTION_PREFIX . $option->getId())->getId(),
            'key' => $value['secret_key']
        ];

        $value['url'] = ['route' => $this->configuratorOptionDownloadUrl, 'params' => $customOptionUrlParams];
        try {
            return $this->getOptionHtml($value);
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
            return '';
        }
    }

    /**
     * Format File option html
     *
     * @param string|array $optionValue Serialized string of option data or its data array
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptionHtml($optionValue)
    {
        $value = $this->unserializeValue($optionValue);
        try {
            $sizes = $this->prepareSize($value);

            $urlRoute = !empty($value['url']['route']) ? $value['url']['route'] : '';
            $urlParams = !empty($value['url']['params']) ? $value['url']['params'] : [];
            $title = !empty($value['title']) ? $value['title'] : '';

            return sprintf(
                '<a href="%s" target="_blank">%s</a> %s',
                $this->_getOptionDownloadUrl($urlRoute, $urlParams),
                $this->escaper->escapeHtml($title),
                $sizes
            );
        } catch (\Exception $e) {
            throw new LocalizedException(__('The file options format is not valid.'));
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param string|array $value
     * @return array
     */
    private function unserializeValue($value)
    {
        if (is_array($value)) {
            return $value;
        } elseif (is_string($value) && !empty($value)) {
            return $this->serializer->unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * @param array $value
     * @return string
     */
    private function prepareSize($value)
    {
        $sizes = '';
        if (!empty($value['width']) && !empty($value['height']) && $value['width'] > 0 && $value['height'] > 0) {
            $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . __('px.');
        }
        return $sizes;
    }

    /**
     * Return URL for option file download
     *
     * @param string|null $route
     * @param array|null $params
     * @return string
     */
    protected function _getOptionDownloadUrl($route, $params)
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
