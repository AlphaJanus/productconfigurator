<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 03.10.18
 * Time: 10:10
 */

namespace Netzexpert\ProductConfigurator\Plugin\Sales\Block\Adminhtml\Items\Column\DefaultColumn;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;

class AddConfiguratorOptionsToOrderItem
{
    /**
     * @param DefaultColumn $defaultColumn
     * @param array $result
     * @return array
     */
    public function afterGetOrderOptions(
        DefaultColumn $defaultColumn,
        $result
    ) {
        if ($options = $defaultColumn->getItem()->getProductOptions()) {
            if (isset($options['configurator_options'])) {
                $result = array_merge($result, $options['configurator_options']);
            }
        }
        return $result;
    }
}
