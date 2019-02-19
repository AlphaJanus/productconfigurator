<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:24
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions;

use Magento\Catalog\Model\Product;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
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

    /** @var FilterProvider  */
    private $filterProvider;

    /** @var Json  */
    protected $json;

    /**
     * AbstractOptions constructor.
     * @param Template\Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterProvider $filterProvider
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterProvider $filterProvider,
        Json $json,
        array $data = []
    ) {
        $this->configuratorOptionRepository         = $configuratorOptionRepository;
        $this->productConfiguratorOptionRepository  = $productConfiguratorOptionRepository;
        $this->searchCriteriaBuilder                = $searchCriteriaBuilder;
        $this->filterProvider                       = $filterProvider;
        $this->json                                 = $json;
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

    /**
     * @return array
     */
    public function getParentOptionDefaultValues()
    {
        $defaultValues = [];
        $parentIds = $this->option->getParentOption();
        if ($parentIds) {
            $this->searchCriteriaBuilder
                ->addFilter('product_id', $this->getProduct()->getId())
                ->addFilter('configurator_option_id', $parentIds, 'in');
            $searchCriteria = $this->searchCriteriaBuilder->create();
            try {
                $parentProductOptions = $this->productConfiguratorOptionRepository->getList($searchCriteria);
                if ($parentProductOptions->getTotalCount()) {
                    foreach ($parentProductOptions->getItems() as $option) {
                        $configuredValue = $this->getProduct()
                            ->getPreconfiguredValues()
                            ->getData('configurator_options/' . $option->getId());
                        if ($configuredValue) {
                            $defaultValues[$option->getConfiguratorOptionId()] = $configuredValue;
                            continue;
                        }

                        $defaultValue = null;

                        try {
                            $parentOption = $this->configuratorOptionRepository->get($option->getConfiguratorOptionId());
                        } catch (NoSuchEntityException $exception) {
                            $this->_logger->error($exception->getMessage());
                            continue;
                        }
                        $values = $parentOption->getValues();
                        if (is_array($values)) {
                            foreach ($values as $value) {
                                if ($value['is_default']) {
                                    $defaultValues[$parentOption->getId()] = $value['value_id'];
                                }
                            }
                        }
                    }
                }
            } catch (LocalizedException $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }
        return $defaultValues;
    }

    public function getValuesData()
    {
        return $this->option->getValuesData();
    }

    public function getAvailableOptionsCount()
    {
        $availableOptions = [];
        foreach ($this->getValuesData() as $value) {
            if ($value['is_dependent']) {
                $parentDefaults = $this->getParentOptionDefaultValues();
                if (!$this->isAllowed($parentDefaults, $value['dependencies'])) {
                    continue;
                }
            }
            $availableOptions[] = $value;
        }
        return count($availableOptions);
    }

    public function getOptionDescription()
    {
        try {
            return $this->filterProvider->getPageFilter()->filter($this->option->getData('description'));
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            return $this->option->getData('description');
        }
    }

    /**
     * @param $parentDefaults array
     * @param $dependencies string
     * @return bool
     */
    public function isAllowed($parentDefaults, $dependencies)
    {
        if (!is_array($parentDefaults)) {
            return false;
        }
        $isAllowed = true;
        $dependencies = $this->mapDependencies($dependencies);

        foreach ($parentDefaults as $optionId => $defaultValue) {
            if (!in_array($defaultValue, $dependencies[$optionId])) {
                $isAllowed = false;
            }
        }
        return $isAllowed;
    }

    /**
     * @param $dependencies string
     * @return array
     */
    protected function mapDependencies($dependencies)
    {
        $result = [];
        $dependencies = $this->json->unserialize($dependencies);
        foreach ($dependencies as $dep) {
            $result[$dep['id']] = (!empty($dep['values'])) ? $dep['values'] : [];
        }
        return $result;
    }
}
