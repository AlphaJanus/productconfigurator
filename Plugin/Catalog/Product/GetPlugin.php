<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 15:24
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Product;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class GetPlugin
{
    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionsGroupRepositoryInterface  */
    private $groupRepository;

    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * GetPlugin constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository
     * @param ProductExtensionFactory $productExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository,
        ProductExtensionFactory $productExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->groupRepository              = $groupRepository;
        $this->productExtensionFactory      = $productExtensionFactory;
        $this->logger                       = $logger;
    }

    public function afterGet(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $this->searchCriteriaBuilder->addFilter('product_id', $product->getId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $configuratorOptions = null;
            try {
                $configuratorOptions = $this->configuratorOptionRepository->getList($searchCriteria);
                $optionsGroups = $this->groupRepository->getList($searchCriteria);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
            if ($configuratorOptions && $configuratorOptions->getTotalCount()) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptions($configuratorOptions->getItems());
            }
            if ($optionsGroups->getTotalCount()) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptionsGroups($optionsGroups->getItems());
            }
        }
        return $product;
    }
}
