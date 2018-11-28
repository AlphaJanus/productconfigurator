<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 11.07.18
 * Time: 9:45
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsGroupsProcessor;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsProcessor;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionVariantProcessor;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;

class ProductSaveProcessor
{
    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var ProductConfiguratorOptionsGroupsProcessor  */
    private $groupProcessor;

    /** @var ProductConfiguratorOptionsProcessor  */
    private $optionsProcessor;

    /** @var ProductConfiguratorOptionVariantProcessor  */
    private $variantProcessor;

    /**
     * ProductSaveProcessor constructor.
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ProductConfiguratorOptionsGroupsProcessor $groupProcessor
     * @param ProductConfiguratorOptionsProcessor $optionsProcessor
     * @param ProductConfiguratorOptionVariantProcessor $variantProcessor
     */
    public function __construct(
        ProductExtensionFactory $productExtensionFactory,
        ProductConfiguratorOptionsGroupsProcessor $groupProcessor,
        ProductConfiguratorOptionsProcessor $optionsProcessor,
        ProductConfiguratorOptionVariantProcessor $variantProcessor
    ) {
        $this->productExtensionFactory              = $productExtensionFactory;
        $this->groupProcessor                       = $groupProcessor;
        $this->optionsProcessor                     = $optionsProcessor;
        $this->variantProcessor                     = $variantProcessor;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function process($product)
    {
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $extensionAttributes = $product->getExtensionAttributes();
            $productExtension = $extensionAttributes ?
                $extensionAttributes : $this->productExtensionFactory->create();
            $originalOptions = ($productExtension->getConfiguratorOptions()) ?
                $productExtension->getConfiguratorOptions() : [];
            $originalGroups = ($productExtension->getConfiguratorOptionsGroups())
                ? $productExtension->getConfiguratorOptionsGroups() : [];
            $options_groups = $product->getData('configurator_option_groups');
            $groups = $this->groupProcessor->process($product, $options_groups, $originalGroups);
            $options = $this->optionsProcessor->process($product, $groups, $originalOptions);
            $this->variantProcessor->process($product, $options);
        }
        return $product;
    }
}
