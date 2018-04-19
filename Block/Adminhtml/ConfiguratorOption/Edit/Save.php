<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 16.04.18
 * Time: 14:38
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

class Save extends Generic
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Option'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'configurator_option_form.configurator_option_form',
                                'actionName' => 'save',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
