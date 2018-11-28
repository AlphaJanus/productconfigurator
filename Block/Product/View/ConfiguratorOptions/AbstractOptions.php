<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:24
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions;

use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;

class AbstractOptions extends \Magento\Framework\View\Element\Template
{
    /** @var Product */
    private $product;

    /** @var ProductConfiguratorOption */
    private $option;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $productConfiguratorOptionRepository;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /**
     * AbstractOptions constructor.
     * @param Template\Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->configuratorOptionRepository         = $configuratorOptionRepository;
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->searchCriteriaBuilder                = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Set Product object
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct($product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set option
     *
     * @param ProductConfiguratorOptionInterface $option
     * @return $this
     */
    public function setOption($option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return ProductConfiguratorOption
     */
    public function getOption()
    {
        return $this->option;
    }

    public function getParentOptionDefaultValue()
    {
        $parentId = $this->option->getParentOption();
        $this->searchCriteriaBuilder
            ->addFilter('product_id', $this->getProduct()->getId())
            ->addFilter('configurator_option_id', $parentId);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $parentProductOptions = $this->productConfiguratorOptionRepository->getList($searchCriteria);
            if ($parentProductOptions->getTotalCount()) {
                $items = $parentProductOptions->getItems();
                $parentProductOption = array_shift($items);
                $configuredValue = $this->getProduct()
                    ->getPreconfiguredValues()
                    ->getData('configurator_options/' . $parentProductOption->getId());
                if ($configuredValue) {
                    return $configuredValue;
                }
            }
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception->getMessage());
        }

        $defaultValue = null;
        if ($parentId != '0') {
            try {
                $parentOption = $this->configuratorOptionRepository->get($parentId);
            } catch (NoSuchEntityException $exception) {
                $this->_logger->error($exception->getMessage());
                return $defaultValue;
            }
            $values = $parentOption->getValues();
            if (is_array($values)) {
                foreach ($values as $value) {
                    if ($value['is_default']) {
                        $defaultValue = $value['value_id'];
                    }
                }
            }
        }
        return $defaultValue;
    }

    public function getValuesData()
    {
        return $this->option->getValuesData();
    }

    public function getAvailableOptionsCount()
    {
        $availableOptions = [];
        foreach ($this->getValuesData() as $value) {
            if ($value['is_dependent']
                && !in_array($this->getParentOptionDefaultValue(), explode(',', $value['allowed_variants']))) {
                continue;
            }
            $availableOptions[] = $value;
        }
        return count($availableOptions);
    }
}
