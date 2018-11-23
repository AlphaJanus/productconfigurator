<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 03.10.18
 * Time: 12:55
 */

namespace Netzexpert\ProductConfigurator\Plugin\Sales\Block\Order\Email\Items\Order\DefaultOrder;

use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;

class AddConfiguratorOptionsToOrderEmail
{
    /**
     * @param DefaultOrder $defaultOrderBlock
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(
        DefaultOrder $defaultOrderBlock,
        $result
    ) {
        $options = $defaultOrderBlock->getItem()->getProductOptions();
        if ($options) {
            if (isset($options['configurator_options'])) {
                $result = array_merge($result, $options['configurator_options']);
            }
        }
        return $result;
    }
}
