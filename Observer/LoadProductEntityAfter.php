<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 15:47
 */

namespace Netzexpert\ProductConfigurator\Observer;


use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class LoadProductEntityAfter implements ObserverInterface
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
     * LoadProductEntityAfter constructor.
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

    public function execute(Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getData('product');
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $this->searchCriteriaBuilder->addFilter('product_id', $product->getId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            try {
                $configuratorOptions = $this->configuratorOptionRepository->getList($searchCriteria);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
            if ($configuratorOptions->getTotalCount()) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptions($configuratorOptions->getItems());
            }
        }
    }
}
