<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 02.10.18
 * Time: 11:22
 */

namespace Netzexpert\ProductConfigurator\Plugin\Quote\Model\Quote\Item\Processor;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Processor;

class Plugin
{
    /**
     * @param Processor $processor
     * @param Item $item
     * @param DataObject $request
     * @param Product $candidate
     * @return array
     */
    public function beforePrepare(
        Processor $processor,
        Item $item,
        DataObject $request,
        Product $candidate
    ) {
        if ($configuredPrice = $request->getData('configured_price')) {
            $request->setData('custom_price', $configuredPrice);
        }
        return [$item, $request, $candidate];
    }
}
