<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 19.04.18
 * Time: 14:37
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

class SaveAndContinue extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
