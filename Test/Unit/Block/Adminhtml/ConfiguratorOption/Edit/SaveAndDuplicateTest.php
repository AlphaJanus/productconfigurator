<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 14.01.19
 * Time: 17:16
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\SaveAndDuplicate;

class SaveAndDuplicateTest extends GenericTest
{

    public function testGetButtonData()
    {
        $this->assertEquals(
            [
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
                    'form-role' => 'save',
                ],
                'sort_order' => 85,
            ],
            $this->getModel(SaveAndDuplicate::class)->getButtonData()
        );
    }
}
