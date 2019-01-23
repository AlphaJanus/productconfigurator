<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.01.19
 * Time: 13:11
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

class SaveAndDuplicate extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save And Duplicate'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'configurator_option_form.configurator_option_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'duplicate'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'sort_order' => 85,
        ];
    }
}
