<?php
/**
 * Created by andrew.
 * Date: 25.11.18
 * Time: 15:26
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Backend\Block\Template\Context;
use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\ExpressionHint;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\CollectionFactory;
use PHPUnit\Framework\TestCase;

class ExpressionHintTest extends TestCase
{

    /**
     * @var CollectionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactory;

    /**
     * @var Collection | \PHPUnit_Framework_MockObject_MockObject
     */
    private $collection;

    /**
     * @var Context | \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ExpressionHint | \PHPUnit_Framework_MockObject_MockObject
     */
    private $block;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['addFieldToFilter'])
            ->getMock();
        $this->collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->block = $objectManager->getObject(
            ExpressionHint::class,
            [
                'context'           => $this->contextMock,
                'collectionFactory' => $this->collectionFactory
            ]
        );
    }

    public function testGetOptions()
    {
        $id = 1;
        $this->block->setData('current_id', $id);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['neq' => $id]);
        $this->assertEquals($collection, $this->block->getOptions());
    }
}
