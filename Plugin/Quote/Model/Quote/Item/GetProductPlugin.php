<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.10.18
 * Time: 15:40
 */

namespace Netzexpert\ProductConfigurator\Plugin\Quote\Model\Quote\Item;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class GetProductPlugin
{

    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * GetPlugin constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductExtensionFactory $productExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductExtensionFactory $productExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->productExtensionFactory      = $productExtensionFactory;
        $this->logger                       = $logger;
    }

    public function afterGetProduct(
        Item $item,
        Product $product
    ) {
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $this->searchCriteriaBuilder->addFilter('product_id', $product->getId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $configuratorOptions = null;
            try {
                $configuratorOptions = $this->configuratorOptionRepository->getList($searchCriteria);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
            if ($configuratorOptions && $configuratorOptions->getTotalCount()) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptions($configuratorOptions->getItems());
            }
        }
        return $product;
    }
}
