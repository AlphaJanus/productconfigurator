<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 09.01.19
 * Time: 12:50
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionsProcessor;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\CollectionFactory;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionsGroup\Collection
    as GroupCollection;
use Netzexpert\ProductConfigurator\Test\Unit\Model\AbstractModelTest;
use Psr\Log\LoggerInterface;

class ProductConfiguratorOptionsProcessorTest extends AbstractModelTest
{
    /** @var ProductInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $product;

    /** @var ProductConfiguratorOptionRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $optionRepository;

    /** @var ProductConfiguratorOption | \PHPUnit_Framework_MockObject_MockObject */
    private $option;

    /** @var ProductConfiguratorOptionInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $optionFactory;

    /** @var Collection | \PHPUnit_Framework_MockObject_MockObject */
    private $collection;

    /** @var CollectionFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $collectionFactory;

    /** @var ProductConfiguratorOptionsGroupInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $group;

    /** @var GroupCollection | \PHPUnit_Framework_MockObject_MockObject */
    private $groupCollection;

    /** @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var CouldNotDeleteException | \PHPUnit_Framework_MockObject_MockObject */
    private $couldNotDelete;

    /** @var NoSuchEntityException | \PHPUnit_Framework_MockObject_MockObject */
    private $noSuchEntity;

    /** @var \Exception | \PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /** @var ProductConfiguratorOptionsProcessor | \PHPUnit_Framework_MockObject_MockObject */
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
        $this->option               = $this->getMock(ProductConfiguratorOption::class);
        $this->optionRepository     = $this->getMock(ProductConfiguratorOptionRepositoryInterface::class);
        $this->optionFactory        = $this->getMock(ProductConfiguratorOptionInterfaceFactory::class);
        $this->collection           = $this->getMock(Collection::class);
        $this->collectionFactory    = $this->getMock(CollectionFactory::class);
        $this->group                = $this->getMock(ProductConfiguratorOptionsGroupInterface::class);
        $this->groupCollection      = $this->getMock(GroupCollection::class);
        $this->couldNotDelete       = $this->getMock(CouldNotDeleteException::class);
        $this->noSuchEntity         = $this->getMock(NoSuchEntityException::class);
        $this->logger               = $this->getMock(LoggerInterface::class);
        $this->exception             = $this->getMock(\Exception::class);
        $this->optionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->option);
        $this->collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        $this->collection->expects($this->any())
            ->method('getItemById')
            ->willReturn($this->option);

        $this->model = new ProductConfiguratorOptionsProcessor(
            $this->optionRepository,
            $this->optionFactory,
            $this->collectionFactory,
            $this->logger
        );
    }

    /**
     * @param $groupData array
     * @param $originalOptionsData array
     * @param $$isDuplicate bool
     * @dataProvider processDataProvider
     */
    public function testProcess($groupData, $originalOptionsData, $isDuplicate)
    {
        // for deleteOptions()
        $iteratorMock = new \ArrayIterator([$this->group]);
        $this->group->expects($this->any())
            ->method('getId')
            ->willReturn($groupData['id']);
        $this->group->expects($this->any())
            ->method('getData')
            ->with('assigned_configurator_options')
            ->willReturn($groupData['assigned_configurator_options']);

        $this->groupCollection->expects($this->any())
            ->method('getIterator')
            ->willReturn($iteratorMock);
        $this->option->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $originalOption = $this->getMock(ProductConfiguratorOption::class);
        $originalOption->expects($this->any())
            ->method('getId')
            ->willReturn($originalOptionsData['option_id']);
        $originalOptions = [
            $originalOptionsData['group_id'] => [
                'group_name' => 'Test',
                'options' => [$originalOption]
            ]
        ];
        if ($groupData['id'] == 3) {
            $this->optionRepository->expects($this->once())
                ->method('deleteById')
                ->with(1)
                ->willThrowException($this->couldNotDelete);
            $this->logger->expects($this->once())
                ->method('error');
        }
        if ($groupData['id'] == 4) {
            $this->optionRepository->expects($this->once())
                ->method('deleteById')
                ->with(2)
                ->willThrowException($this->noSuchEntity);
            $this->logger->expects($this->once())
                ->method('error');
        }

        // for saveOptions()
        $this->product->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $this->product->expects($this->any())
            ->method('getData')
            ->with('is_duplicate')
            ->willReturn($isDuplicate);
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $this->option->expects($this->any())
            ->method('setData')
            ->willReturnSelf();
        $this->option->expects($this->any())
            ->method('setParentOption')
            ->willReturnSelf();
        $this->option->expects($this->any())
            ->method('setEnabledOnParentVariants')
            ->willReturnSelf();
        $this->option->expects($this->any())
            ->method('setProductId')
            ->willReturnSelf();
        if ($groupData['id'] == 2) {
            $this->collection->expects($this->once())
                ->method('addItem')
                ->with($this->option)
                ->willThrowException($this->exception);
            $this->logger->expects($this->once())
                ->method('error');
        }

        if ($isDuplicate) {
            $this->collection->expects($this->once())
                ->method('removeItemByKey')
                ->with(40);
        }

        $this->assertEquals(
            $this->collection,
            $this->model->process($this->product, $this->groupCollection, $originalOptions)
        );
        $this->assertEquals(
            null,
            $this->model->process($this->product, null, $originalOptions)
        );
    }

    public function processDataProvider()
    {
        return [
            [
                'groupData' => [
                    'id' => 1,
                    'assigned_configurator_options' => [
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '5',
                    'option_id'   => '10'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 1,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '5',
                    'option_id'   => '10'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 1,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '1',
                            'configurator_option_id'    => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '5',
                    'option_id'   => '10'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 2,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '',
                            'configurator_option_id'    => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '5',
                    'option_id'   => '10'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 3,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '',
                            'configurator_option_id'    => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '3',
                    'option_id'   => '1'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 4,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '',
                            'configurator_option_id'    => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '4',
                    'option_id'   => '2'
                ],
                'is_duplicate' => false
            ],
            [
                'groupData' => [
                    'id' => 4,
                    'assigned_configurator_options' => [
                        [
                            'option_id'                 => '40',
                            'configurator_option_id'    => '1'
                        ]
                    ]
                ],
                'originalOptionsData' => [
                    'group_id' => '4',
                    'option_id'   => '2'
                ],
                'is_duplicate' => true
            ]
        ];
    }
}
