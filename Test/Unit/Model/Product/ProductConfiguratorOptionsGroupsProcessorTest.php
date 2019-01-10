<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 08.01.19
 * Time: 15:05
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsGroupRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsGroup;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsGroupsProcessor;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\CollectionFactory;
use Netzexpert\ProductConfigurator\Test\Unit\Model\AbstractModelTest;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionsGroupsProcessorTest extends AbstractModelTest
{

    /** @var ProductInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $product;

    /** @var ProductConfiguratorOptionsGroup |  \PHPUnit_Framework_MockObject_MockObject */
    private $group;

    /** @var ProductConfiguratorOptionsGroupRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $groupRepository;

    /** @var ProductConfiguratorOptionsGroupInterfaceFactory |  \PHPUnit_Framework_MockObject_MockObject */
    private $groupFactory;

    /** @var Collection | \PHPUnit_Framework_MockObject_MockObject */
    private $collection;

    /** @var CollectionFactory |  \PHPUnit_Framework_MockObject_MockObject */
    private $collectionFactory;

    /** @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var CouldNotDeleteException | \PHPUnit_Framework_MockObject_MockObject */
    private $couldNotDelete;

    /** @var NoSuchEntityException | \PHPUnit_Framework_MockObject_MockObject */
    private $noSuchEntity;

    /** @var \Exception | \PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /** @var ProductConfiguratorOptionsGroupsProcessor | \PHPUnit_Framework_MockObject_MockObject */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $productMethods = get_class_methods(ProductInterface::class);
        $productMethods[] = 'getData';
        $this->product = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->setMethods($productMethods)
            ->getMock();
        $this->group                = $this->getMock(ProductConfiguratorOptionsGroup::class);
        $this->groupRepository      = $this->getMock(ProductConfiguratorOptionsGroupRepositoryInterface::class);
        $this->groupFactory         = $this->getMock(ProductConfiguratorOptionsGroupInterfaceFactory::class);
        $this->collection           = $this->getMock(Collection::class);
        $this->collectionFactory    = $this->getMock(CollectionFactory::class);
        $this->logger               = $this->getMock(LoggerInterface::class);
        $this->couldNotDelete       = $this->getMock(CouldNotDeleteException::class);
        $this->noSuchEntity         = $this->getMock(NoSuchEntityException::class);
        $this->exception             = $this->getMock(\Exception::class);
        $this->groupRepository->expects($this->any())
            ->method('get')
            ->willReturn($this->group);
        $this->groupFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->group);
        $this->collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        $this->model = new ProductConfiguratorOptionsGroupsProcessor(
            $this->groupRepository,
            $this->groupFactory,
            $this->collectionFactory,
            $this->logger
        );
    }

    /**
     * @param array $options_groups
     * @param array $originalGroups
     * @param int $groupId
     * @dataProvider processDataProvider
     */
    public function testProcess($options_groups, $originalGroups, $groupId = null)
    {
        $this->product->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $this->product->expects($this->any())
            ->method('getData')
            ->with('is_duplicate')
            ->willReturn(false);
        if (!empty($options_groups)) {
            $this->collection->expects($this->once())
                ->method('addFieldToFilter')
                ->with('product_id', $this->product->getId())
                ->willReturnSelf();
            $this->collection->expects($this->any())
                ->method('getItemById')
                ->willReturn($this->group);
            $this->group->expects($this->any())
                ->method('setData')
                ->willReturnSelf();
            $this->collection->expects($this->once())
                ->method('walk');
        }
        switch ($groupId) {
            case 1:
                $this->groupRepository->expects($this->once())
                    ->method('deleteById')
                    ->with(1);
                break;
            case 2:
                $this->collection->expects($this->once())
                    ->method('addItem')
                    ->with($this->group)
                    ->willThrowException($this->exception);
                $this->groupRepository->expects($this->once())
                    ->method('deleteById')
                    ->with(2)
                    ->willThrowException($this->couldNotDelete);
                $this->logger->expects($this->exactly(2))
                    ->method('error');
                break;
            case 3:
                $this->groupRepository->expects($this->once())
                    ->method('deleteById')
                    ->with(3)
                    ->willThrowException($this->noSuchEntity);
                $this->logger->expects($this->once())
                    ->method('error');
                break;
        }
        $this->model->process($this->product, $options_groups, $originalGroups);
    }

    public function processDataProvider()
    {
        $originalGroup1 = $this->getMock(ProductConfiguratorOptionsGroup::class);
        $originalGroup1->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $originalGroup2 = $this->getMock(ProductConfiguratorOptionsGroup::class);
        $originalGroup2->expects($this->any())
            ->method('getId')
            ->willReturn(2);
        $originalGroup3 = $this->getMock(ProductConfiguratorOptionsGroup::class);
        $originalGroup3->expects($this->any())
            ->method('getId')
            ->willReturn(3);
        return [
            [
                [
                    [
                        'group_id'      => '',
                        'product_id'    => '',
                        'name'          => 'Test',
                        'position'      => 1,
                        'initialize'    => 1,
                        'record_id'     => 0
                    ]
                ],
                []
            ],
            [
                [
                ],
                [
                    $originalGroup1
                ],
                1
            ],
            [
                [
                    [
                        'group_id'      => '5',
                        'product_id'    => '',
                        'name'          => 'Test',
                        'position'      => 1,
                        'initialize'    => 1,
                        'record_id'     => 0
                    ]
                ],
                [
                    $originalGroup1
                ],
                1
            ],
            [
                [
                    [
                        'group_id'      => '',
                        'product_id'    => '',
                        'name'          => 'Test',
                        'position'      => 1,
                        'initialize'    => 1,
                        'record_id'     => 0
                    ]
                ],
                [
                    $originalGroup2
                ],
                2
            ],
            [
                [
                    [
                        'group_id'      => '',
                        'product_id'    => '',
                        'name'          => 'Test',
                        'position'      => 1,
                        'initialize'    => 1,
                        'record_id'     => 0
                    ]
                ],
                [
                    $originalGroup3
                ],
                3
            ]
        ];
    }
}
