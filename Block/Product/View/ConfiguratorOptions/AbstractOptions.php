<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:24
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;

class AbstractOptions extends \Magento\Framework\View\Element\Template
{
    /** @var ProductInterface */
    private $product;

    /** @var ProductConfiguratorOption */
    private $option;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var Json  */
    private $json;

    /**
     * AbstractOptions constructor.
     * @param Template\Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        Json $json,
        array $data = []
    ) {
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->json = $json;
        parent::__construct($context, $data);
    }

    /**
     * Set Product object
     *
     * @param ProductInterface $product
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
     * @return ProductInterface
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
        $defaultValue = null;
        if ($parentId != '0') {
            try {
                $parentOption = $this->configuratorOptionRepository->get($parentId);
            } catch (NoSuchEntityException $exception) {
                $this->_logger->error($exception->getMessage());
                return $defaultValue;
            }
            if (is_array($values = $parentOption->getValues())) {
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
        $valuesData = $this->option->getValuesData();
        if ($valuesData) {
            return $this->json->unserialize($valuesData);
        }
        return $valuesData;
    }

    public function getAvailableOptionsCount()
    {
        $availableOptions = [];
        foreach ($this->getValuesData() as $value) {
            if ($value['is_dependent'] && !in_array($this->getParentOptionDefaultValue(), $value['allowed_variants'])) {
                continue;
            }
            $availableOptions[] = $value;
        }
        return count($availableOptions);
    }
}
