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
use Netzexpert\ProductConfigurator\Model\ProductConfiguratorOptionsProcessor;

class SaveProductEntityAfter implements ObserverInterface
{
    /** @var ProductConfiguratorOptionsProcessor  */
    private $configuratorOptionsProcessor;

    /**
     * SaveProductEntityAfter constructor.
     * @param ProductConfiguratorOptionsProcessor $configuratorOptionsProcessor
     */
    public function __construct(
        ProductConfiguratorOptionsProcessor $configuratorOptionsProcessor
    ) {
        $this->configuratorOptionsProcessor = $configuratorOptionsProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');
        $this->configuratorOptionsProcessor->process($product);
    }
}
