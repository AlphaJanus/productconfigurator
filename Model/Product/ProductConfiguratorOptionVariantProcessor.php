<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 17:29
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\Collection
    as OptionsCollection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant\CollectionFactory;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionVariantProcessor
{

    /** @var ProductConfiguratorOptionVariantFactory  */
    private $variantFactory;

    /** @var CollectionFactory  */
    private $collectionFactory;

    public function __construct(
        ProductConfiguratorOptionVariantFactory $variantFactory,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->variantFactory       = $variantFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->logger               = $logger;
    }

    /**
     * @param $product ProductInterface
     * @param $option OptionsCollection
     */
    public function process($product, $options)
    {
        $productId = $product->getId();
        if (!empty($options)) {
            /** @var ProductConfiguratorOption $option */
            foreach ($options as $option) {
                /** @var Collection $collection */
                $collection = $this->collectionFactory->create()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('option_id', $option->getId());
                if (empty($option->getData('values'))) {
                    continue;
                }
                foreach ($option->getData('values') as $variant) {
                    if (!$option->getParentOption()) {
                        unset($variant['allowed_variants']);
                    }
                    if (!empty($variant['variant_id'])) {
                        $collection->getItemById($variant['variant_id'])
                            ->setData($variant)
                            ->setProductId($productId)
                            ->setOptionId($option->getId())
                            ->setAllowedVariants(
                                (!empty($variant['allowed_variants'])) ?
                                    implode(',', $variant['allowed_variants']) : null
                            )->setConfiguratorOptionId($option->getConfiguratorOptionId());
                    } else {
                        $variant = $this->variantFactory->create()
                            ->setData($variant)
                            ->setProductId($productId)
                            ->setOptionId($option->getId())
                            ->setAllowedVariants(
                                (!empty($variant['allowed_variants'])) ?
                                    implode(',', $variant['allowed_variants']) : null
                            )->setConfiguratorOptionId($option->getConfiguratorOptionId());
                        try {
                            $collection->addItem($variant);
                        } catch (\Exception $exception) {
                            $this->logger->error($exception->getMessage());
                        }
                    }
                }
                $collection->walk('save');
            }
        }
    }
}
