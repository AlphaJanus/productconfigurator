<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 17:29
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;
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

    /** @var Json  */
    private $serializer;

    public function __construct(
        ProductConfiguratorOptionVariantFactory $variantFactory,
        CollectionFactory $collectionFactory,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->variantFactory       = $variantFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->serializer           = $serializer;
        $this->logger               = $logger;
    }

    /**
     * @param $product ProductInterface | Product
     * @param $options OptionsCollection
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
                    $dependencies = (!empty($variant['dependencies'])) ? $variant['dependencies'] : [];
                    if (!empty($variant['variant_id']) && !$product->getData('is_duplicate')) {
                        $collection->getItemById($variant['variant_id'])
                            ->setData($variant)
                            ->setProductId($productId)
                            ->setOptionId($option->getId())
                            ->setDependencies(/*$this->serializer->serialize(*/$dependencies/*)*/)
                            ->setConfiguratorOptionId($option->getConfiguratorOptionId());
                    } else {
                        $variant = $this->variantFactory->create()
                            ->setData($variant)
                            ->setProductId($productId)
                            ->setOptionId($option->getId())
                            ->setDependencies(/*$this->serializer->serialize(*/$dependencies/*)*/)
                            ->setConfiguratorOptionId($option->getConfiguratorOptionId());
                        if (!$variant['variant_id'] || $product->getData('is_duplicate')) {
                            $variant->setId(null);
                        }
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
