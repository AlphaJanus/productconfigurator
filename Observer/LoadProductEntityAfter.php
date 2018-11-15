<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 15:47
 */

namespace Netzexpert\ProductConfigurator\Observer;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class LoadProductEntityAfter implements ObserverInterface
{
    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    private $sortOrderBuilder;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionsGroupRepositoryInterface  */
    private $groupRepository;

    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * LoadProductEntityAfter constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository
     * @param ProductExtensionFactory $productExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionsGroupRepositoryInterface $groupRepository,
        ProductExtensionFactory $productExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->sortOrderBuilder             = $sortOrderBuilder;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->groupRepository              = $groupRepository;
        $this->productExtensionFactory      = $productExtensionFactory;
        $this->logger                       = $logger;
    }

    public function execute(Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getData('product');
        $configuratorOptions = [];
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $this->sortOrderBuilder->setField('position')->setDirection(SortOrder::SORT_ASC);
            $sortOrder = $this->sortOrderBuilder->create();
            $this->searchCriteriaBuilder
                ->addFilter('product_id', $product->getId())
                ->addSortOrder($sortOrder);
            $searchCriteria = $this->searchCriteriaBuilder->create();

            try {
                $optionsGroups = $this->groupRepository->getList($searchCriteria);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
            if ($optionsGroups->getTotalCount()) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptionsGroups($optionsGroups->getItems());
                foreach ($optionsGroups->getItems() as $group) {
                    $this->searchCriteriaBuilder
                        ->addFilter('product_id', $product->getId())
                        ->addFilter('group_id', $group->getId())
                        ->addSortOrder($sortOrder);
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    try {
                        $configuratorOptions[$group->getId()] = [
                            'group_name'    => $group->getName(),
                            'options' => $this->configuratorOptionRepository->getList($searchCriteria)->getItems()
                        ];
                    } catch (LocalizedException $exception) {
                        $this->logger->error($exception->getMessage());
                    }
                }
            }
            if (count($configuratorOptions)) {
                $extensionAttributes = $product->getExtensionAttributes();
                $productExtension = $extensionAttributes ?
                    $extensionAttributes : $this->productExtensionFactory->create();
                $productExtension->setConfiguratorOptions($configuratorOptions);
            }
        }
    }
}
