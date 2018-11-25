<?php
/**
 * Created by andrew.
 * Date: 25.11.18
 * Time: 16:27
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\SaveAndContinue;

class SaveAndContinueTest extends GenericTest
{

    public function testGetButtonData()
    {
        $this->assertEquals(
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit'],
                    ],
                ],
                'sort_order' => 80,
            ],
            $this->getModel(SaveAndContinue::class)->getButtonData()
        );
    }
}
