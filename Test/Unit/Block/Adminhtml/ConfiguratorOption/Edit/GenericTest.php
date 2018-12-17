<?php
/**
 * Created by andrew.
 * Date: 24.11.18
 * Time: 12:45
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Framework\Registry;
use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Generic;
use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GenericTest extends TestCase
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Context| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $class
     * @return Generic | object
     */
    protected function getModel($class = Generic::class)
    {
        return $this->objectManager->getObject($class, [
            'context' => $this->contextMock,
            'registry' => $this->registryMock,
        ]);
    }

    public function testGetUrl()
    {
        $this->contextMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('test_url');

        $this->assertSame('test_url', $this->getModel()->getUrl());
    }

    public function testGetButtonData()
    {
        $this->assertEquals([], $this->getModel()->getButtonData());
    }
}
