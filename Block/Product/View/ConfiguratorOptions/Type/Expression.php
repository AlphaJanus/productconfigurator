<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 10.09.18
 * Time: 12:21
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class Expression extends AbstractOptions
{
    /** @var ProductExtensionFactory  */
    private $extensionFactory;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /**
     * Expression constructor.
     * @param Template\Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterProvider $filterProvider
     * @param Json $json
     * @param ProductExtensionFactory $extensionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterProvider $filterProvider,
        Json $json,
        ProductExtensionFactory $extensionFactory,
        array $data = []
    ) {
        $this->extensionFactory             = $extensionFactory;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        parent::__construct(
            $context,
            $configuratorOptionRepository,
            $productConfiguratorOptionRepository,
            $searchCriteriaBuilder,
            $filterProvider,
            $json,
            $data
        );
    }

    /**
     * @return \Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface[]|null
     */
    public function getConfiguratorOptions()
    {
        $extensionAttributes = $this->getProduct()->getExtensionAttributes();
        $productExtension = $extensionAttributes ?
            $extensionAttributes : $this->extensionFactory->create();
        $productOptionsGroups = $productExtension->getConfiguratorOptions();
        if (!empty($productOptionsGroups)) {
            foreach ($productOptionsGroups as $productOptionsGroup) {
                foreach ($productOptionsGroup['options'] as &$option) {
                    try {
                        $configuratorOption = $this->configuratorOptionRepository
                            ->get($option->getConfiguratorOptionId());
                    } catch (NoSuchEntityException $exception) {
                        $this->_logger->error($exception->getMessage());
                        continue;
                    }
                    $option->setAdditionalData($configuratorOption->getData());
                }
            }
            return $productOptionsGroups;
        }
        return $productOptionsGroups;
    }

    public function getDependencyJsonConfig()
    {
        $config = [];
        foreach ($this->getConfiguratorOptions() as $optionGroup) {
            foreach ($optionGroup['options'] as $option) {
                $id = $option->getId();
                $valuesData = $option->getValuesData() ? $option->getValuesData() : [];
                $config[$id] = $option->getData();
                $config[$id]['values'] = $valuesData;
                unset($config[$id]['values_data']);
            }
        }

        return $this->json->serialize($config);
    }
}
