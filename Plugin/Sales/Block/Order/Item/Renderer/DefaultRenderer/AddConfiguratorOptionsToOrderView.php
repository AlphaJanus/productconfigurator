<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 03.10.18
 * Time: 13:45
 */

namespace Netzexpert\ProductConfigurator\Plugin\Sales\Block\Order\Item\Renderer\DefaultRenderer;

use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;

class AddConfiguratorOptionsToOrderView
{
    /**
     * @param DefaultRenderer $defaultRenderer
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(
        DefaultRenderer $defaultRenderer,
        $result
    ) {
        if ($defaultRenderer->getItem()->getOrderItem()) {
            $options = $defaultRenderer->getItem()->getOrderItem()->getProductOptions();
            if ($options) {
                if (isset($options['configurator_options'])) {
                    $result = array_merge($result, $options['configurator_options']);
                }
            }
        }
        $options = $defaultRenderer->getItem()->getProductOptions();
        if ($options) {
            if (isset($options['configurator_options'])) {
                $result = array_merge($result, $options['configurator_options']);
            }
        }
        return $result;
    }
}
