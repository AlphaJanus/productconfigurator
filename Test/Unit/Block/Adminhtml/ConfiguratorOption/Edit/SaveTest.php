<?php
/**
 * Created by andrew.
 * Date: 25.11.18
 * Time: 16:14
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Save;

class SaveTest extends GenericTest
{

    public function testGetButtonData()
    {
        $this->assertEquals(
            [
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
            ],
            $this->getModel(Save::class)->getButtonData()
        );
    }
}
