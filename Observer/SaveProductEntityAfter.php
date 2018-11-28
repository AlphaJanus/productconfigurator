<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 05.07.18
 * Time: 16:53
 */

namespace Netzexpert\ProductConfigurator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netzexpert\ProductConfigurator\Model\ProductSaveProcessor;

class SaveProductEntityAfter implements ObserverInterface
{
    /** @var ProductSaveProcessor  */
    private $productSaveProcessor;

    /**
     * SaveProductEntityAfter constructor.
     * @param ProductSaveProcessor $productSaveProcessor
     */
    public function __construct(
        ProductSaveProcessor $productSaveProcessor
    ) {
        $this->productSaveProcessor = $productSaveProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');
        $this->productSaveProcessor->process($product);
    }
}
