<?php
/**
 * Created by andrew.
 * Date: 24.11.18
 * Time: 14:13
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\Delete;
use Psr\Log\LoggerInterface;

class DeleteTest extends GenericTest
{
    /**
     * @var ConfiguratorOptionRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionRepositoryMock;

    /**
     * @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var ConfiguratorOptionInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $option;

    /**
     * @var RequestInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var NoSuchEntityException | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $noSuchEntityException;

    protected function setUp()
    {
        parent::setUp();
        $this->optionRepositoryMock = $this->getMockBuilder(ConfiguratorOptionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->option = $this->getMockBuilder(ConfiguratorOptionInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->optionRepositoryMock->expects($this->any())
            ->method('get')->willReturn($this->option);
        $this->option->expects($this->any())->method('getId')->willReturn(1);
        $this->noSuchEntityException = $this->getMockBuilder(NoSuchEntityException::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $class
     * @return Delete | object
     */
    protected function getModel($class = Delete::class)
    {
        return $this->objectManager->getObject($class, [
            'context' => $this->contextMock,
            'registry' => $this->registryMock,
            'optionRepository' => $this->optionRepositoryMock
        ]);
    }

    /**
     * @param $data int
     * @param $expected int
     * @dataProvider optionIdProvider
     */
    public function testGetOptionId($data, $expected)
    {
        $this->contextMock->expects($this->any())->method('getRequestParam')->with('id')->willReturn($data);
        $this->assertEquals($expected, $this->getModel()->getOptionId());
    }

    public function testGetDeleteUrl()
    {
        $this->contextMock->expects($this->any())->method('getRequestParam')->with('id')->willReturn(1);
        $this->contextMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/delete', ['id'=>1])
            ->willReturn('delete/id/1');
        $this->assertSame('delete/id/1', $this->getModel()->getDeleteUrl());
    }
    public function testGetButtonData()
    {
        $this->contextMock->expects($this->any())->method('getRequestParam')->with('id')->willReturn(1);
        $this->assertEquals(
            [
                'label' => __('Delete Option'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getModel()->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ],
            $this->getModel()->getButtonData()
        );
    }

    public function testGetOptionIdException()
    {
        $this->optionRepositoryMock->expects($this->any())
            ->method('get')->willThrowException($this->noSuchEntityException);
        $this->assertEquals(null, $this->getModel()->getOptionId());
    }

    public function optionIdProvider()
    {
        return [
            [1,1],
            [null,null]
        ];
    }
}
