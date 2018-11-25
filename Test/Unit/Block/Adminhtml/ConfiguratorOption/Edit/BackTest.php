<?php
/**
 * Created by andrew.
 * Date: 24.11.18
 * Time: 12:39
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Back;

class BackTest extends GenericTest
{

    /**
     * @covers \Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Back::getButtonData
     */
    public function testGetButtonData()
    {
        $this->contextMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/', [])
            ->willReturn('/');

        $this->assertEquals(
            [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", '/'),
                'class' => 'back',
                'sort_order' => 10
            ],
            $this->getModel(Back::class)->getButtonData()
        );
    }
}
