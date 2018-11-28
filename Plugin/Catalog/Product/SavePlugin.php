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
use Netzexpert\ProductConfigurator\Model\ProductSaveProcessor;

class SavePlugin
{

    /** @var ProductSaveProcessor  */
    private $productSaveProcessor;

    /**
     * SavePlugin constructor.
     * @param ProductSaveProcessor $productSaveProcessor
     */
    public function __construct(
        ProductSaveProcessor $productSaveProcessor
    ) {
        $this->productSaveProcessor = $productSaveProcessor;
    }

    public function afterSave(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $product = $this->productSaveProcessor->process($product);

        return $product;
    }
}
