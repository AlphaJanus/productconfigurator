<?php
/**
 * Created by andrew.
 * Date: 25.11.18
 * Time: 16:09
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Reset;

class ResetTest extends GenericTest
{

    public function testGetButtonData()
    {
        $this->assertEquals(
            [
                'label' => __('Reset'),
                'class' => 'reset',
                'on_click' => 'location.reload();',
                'sort_order' => 30
            ],
            $this->getModel(Reset::class)->getButtonData()
        );

    }
}
