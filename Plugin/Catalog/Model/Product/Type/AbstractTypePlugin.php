<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 19.09.18
 * Time: 15:58
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;

class AbstractTypePlugin
{
    /** @var ProductExtensionFactory  */
    private $productExtensionFactory;

    /** @var Json  */
    private $serializer;

    public function __construct(
        ProductExtensionFactory $productExtensionFactory,
        Json $serializer
    ) {
        $this->productExtensionFactory  = $productExtensionFactory;
        $this->serializer                     = $serializer;
    }

    /**
     * @param AbstractType $abstractType
     * @param DataObject $buyRequest
     * @param Product $product
     * @param null|string $processMode
     * @return array
     */
    public function beforePrepareForCartAdvanced(
        AbstractType $abstractType,
        DataObject $buyRequest,
        $product,
        $processMode
    ) {
        $options = $this->prepareOptions($buyRequest, $product);
        $optionIds = array_keys($options);
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $product->addCustomOption('configurator_option_ids', implode(',', $optionIds));
            foreach ($options as $optionId => $optionValue) {
                $product->addCustomOption('configurator_option_' . $optionId, $optionValue);
            }
        }
        return [$buyRequest, $product, $processMode];
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @return array
     */
    private function prepareOptions($buyRequest, $product)
    {
        $options = [];
        $extensionAttributes = $product->getExtensionAttributes();
        $productExtension = $extensionAttributes ? $extensionAttributes : $this->productExtensionFactory->create();
        $configuratorOptions = $productExtension->getConfiguratorOptions();
        $requestOptions = $buyRequest->getDataByKey('configurator_options');
        if ($configuratorOptions != null && $requestOptions != null) {
            foreach ($configuratorOptions as $optionGroup) {
                foreach ($optionGroup['options'] as $option) {
                    $optionValue = (!empty($requestOptions[$option->getId()])) ? $requestOptions[$option->getId()] : '';
                    if ($optionValue) {
                        $options[$option->getConfiguratorOptionId()] = $optionValue;
                    }
                }
            }
        }
        return $options;
    }
}
