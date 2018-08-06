<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 10:45
 */

namespace Netzexpert\ProductConfigurator\Plugin\Catalog\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ProductConfiguratorOptionsProcessor;

class SavePlugin
{

    /** @var ProductConfiguratorOptionsProcessor  */
    private $configuratorOptionsProcessor;

    /**
     * SavePlugin constructor.
     * @param ProductConfiguratorOptionsProcessor $configuratorOptionsProcessor
     */
    public function __construct(
        ProductConfiguratorOptionsProcessor $configuratorOptionsProcessor
    ) {
        $this->configuratorOptionsProcessor = $configuratorOptionsProcessor;
    }

    public function afterSave(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $product = $this->configuratorOptionsProcessor->process($product);

        return $product;
    }
}
