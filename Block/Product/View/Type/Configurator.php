<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.08.18
 * Time: 11:35
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\Type;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\ArrayUtils;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions as OptionsBlock;

class Configurator extends AbstractView
{
    /** @var ProductExtensionFactory  */
    private $extensionFactory;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var PricingHelper  */
    private $pricingHelper;

    /** @var CatalogHelper  */
    private $catalogHelper;

    /** @var Json  */
    private $json;

    /** @var DataObjectFactory  */
    private $dataObjectFactory;

    /** @var array | null ConfiguratorOptions */
    private $configuratorOptions;

    /**
     * Configurator constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param ProductExtensionFactory $extensionFactory
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param PricingHelper $pricingHelper
     * @param CatalogHelper $catalogHelper
     * @param Json $json
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        ProductExtensionFactory $extensionFactory,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        PricingHelper $pricingHelper,
        CatalogHelper $catalogHelper,
        Json $json,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->extensionFactory             = $extensionFactory;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->pricingHelper                = $pricingHelper;
        $this->catalogHelper                = $catalogHelper;
        $this->json                         = $json;
        $this->dataObjectFactory            = $dataObjectFactory;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * @return array|null
     */
    public function getConfiguratorOptions()
    {
        if (!empty($this->configuratorOptions)){
            return $this->configuratorOptions;
        }
        $extensionAttributes = $this->getProduct()->getExtensionAttributes();
        $productExtension = $extensionAttributes ?
            $extensionAttributes : $this->extensionFactory->create();
        $productOptions = $productExtension->getConfiguratorOptions();
        if (!empty($productOptions)) {
            foreach ($productOptions as $optionGroup) {
                foreach ($optionGroup['options'] as &$option) {
                    try {
                        $configuratorOption = $this->configuratorOptionRepository
                            ->get($option->getConfiguratorOptionId());
                    } catch (NoSuchEntityException $exception) {
                        $this->_logger->error($exception->getMessage());
                        return null;
                    }
                    $option->setAdditionalData($configuratorOption->getData());
                }
            }
            return $productOptions;
        }
        return $productOptions;
    }

    /**
     * @return int
     */
    public function getOptionsCount()
    {
        $count = 0;
        foreach ($this->getConfiguratorOptions() as $optionGroup) {
            $count += count($optionGroup['options']);
        }
        return $count;
    }

    /**
     * @param ProductConfiguratorOptionInterface $option
     * @return string
     */
    public function getOptionHtml($option)
    {
        $type = $option->getData('type');
        /** @var OptionsBlock $renderer */
        $renderer = $this->getChildBlock('c-' . $type);
        if ($renderer) {
            $renderer->setProduct($this->getProduct())->setOption($option);
        }

        return $this->getChildHtml('c-' . $type, false);
    }

    public function getDependencyJsonConfig()
    {
        $config = [];
        foreach ($this->getConfiguratorOptions() as $optionGroup) {
            foreach ($optionGroup['options'] as $option) {
                $id = $option->getId();
                $valuesData = $option->getValuesData() ? $option->getValuesData() : null;
                $config[$id] = $option->getData();
                $config[$id]['values'] = $valuesData;
                unset($config[$id]['values_data']);
            }
        }

        return $this->json->serialize($config);
    }

    public function getJsonConfig()
    {
        $config = [];
        foreach ($this->getConfiguratorOptions() as $optionGroup) {
            foreach ($optionGroup['options'] as $option) {
                $id = $option->getId();
                if ($option->hasValues()) {
                    $tmpPriceValues = [];
                    foreach ($option->getData('values') as $value) {
                        $valueObj = $this->dataObjectFactory->create();
                        $valueObj->setData($value);
                        $valueObj->setData('product', $this->getProduct());
                        $tmpPriceValues[$valueObj->getData('value_id')] = $this->getPriceConfiguration($valueObj);
                    }

                    $priceValue = $tmpPriceValues;
                } else {
                    $priceValue = $this->getPriceConfiguration($option);
                }

                $config[$id] = $priceValue;
            }
        }

        return $this->json->serialize($config);
    }

    /**
     * Get price configuration
     *
     * @param \Magento\Framework\DataObject|ProductConfiguratorOptionInterface $option
     * @return array
     */
    private function getPriceConfiguration($option)
    {
        $optionPrice = $this->pricingHelper->currency($option->getPrice(), false, false);
        $data = [
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->pricingHelper->currency($option->getRegularPrice(), false, false),
                    'adjustments' => [],
                ],
                'basePrice' => [
                    'amount' => $this->catalogHelper->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->catalogHelper->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
            ],
            'type' => "fixed", //toDo: allow to choose price type: fixed or percent
            'value' => $option->getValue(),
            'name' => $option->getTitle()
        ];
        return $data;
    }
}
