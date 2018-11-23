<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 03.10.18
 * Time: 14:51
 */

namespace Netzexpert\ProductConfigurator\Plugin\Sales\Model\Order\Pdf\Items\AbstractItems;

use Magento\Sales\Model\Order\Pdf\Items\AbstractItems;

class AddConfiguratorOptionsToInvoicePdf
{
    /**
     * @param AbstractItems $subject
     * @param $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetItemOptions(
        AbstractItems $subject,
        $result
    ) {
        if ($subject->getItem()->getOrderItem()) {
            $options = $subject->getItem()->getOrderItem()->getProductOptions();
            if ($options) {
                if (isset($options['configurator_options'])) {
                    $result = array_merge($result, $options['configurator_options']);
                }
            }
        }
        return $result;
    }
}
