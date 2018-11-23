<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 03.10.18
 * Time: 14:05
 */

namespace Netzexpert\ProductConfigurator\Plugin\Sales\Block\Order\Email\Items\DefaultItems;

use Magento\Sales\Block\Order\Email\Items\DefaultItems;

class AddConfiguratorOptionsToInvoiceEmail
{
    /**
     * @param DefaultItems $defaultItemsBlock
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(
        DefaultItems $defaultItemsBlock,
        $result
    ) {
        $options = $defaultItemsBlock->getItem()->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['configurator_options'])) {
                $result = array_merge($result, $options['configurator_options']);
            }
        }
        return $result;
    }
}
