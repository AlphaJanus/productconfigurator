<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 22.12.18
 * Time: 11:52
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\Component\Listing;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Processor;
use Magento\Framework\View\Element\UiComponentInterface;
use Netzexpert\ProductConfigurator\Ui\Component\ColumnFactory;
use Netzexpert\ProductConfigurator\Ui\Component\Listing\Attribute\RepositoryInterface;
use Netzexpert\ProductConfigurator\Ui\Component\Listing\Columns;
use PHPUnit\Framework\TestCase;

class ColumnsTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var Processor | \PHPUnit_Framework_MockObject_MockObject */
    private $processor;

    /** @var ContextInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $contextInterface;

    /** @var UiComponentInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $column;

    /** @var ColumnFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $columnFactory;

    /** @var RepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $attributeRepository;

    /** @var Attribute | \PHPUnit_Framework_MockObject_MockObject */
    private $attribute;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->processor = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextInterface = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->contextInterface->expects($this->any())
            ->method('getProcessor')
            ->willReturn($this->processor);
        $this->column = $this->getMockBuilder(UiComponentInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->columnFactory = $this->getMockBuilder(ColumnFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeRepository = $this->getMockBuilder(RepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList'])
            ->getMockForAbstractClass();
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Columns | object
     */
    private function getModel()
    {
        /*return $this->objectManager->getObject(
            Columns::class,
            [
                $this->contextInterface,
                $this->columnFactory,
                $this->attributeRepository,
                [],
                []
            ]
        );*/
        return new Columns(
            $this->contextInterface,
            $this->columnFactory,
            $this->attributeRepository,
            [],
            []
        );
    }

    /**
     * @param string $frontendType
     * @param array $config
     * @dataProvider getPrepareDataProvider
     */
    public function testPrepare($frontendType, $config)
    {
        $this->attributeRepository->expects($this->once())
            ->method('getList')
            ->willReturn(
                [
                    $this->attribute
                ]
            );
        $this->attribute->expects($this->any())
            ->method('getData')
            ->withConsecutive(['is_visible_in_grid'], ['is_filterable_in_grid'])
            ->willReturnOnConsecutiveCalls(true, true);
        $this->attribute->expects($this->once())
            ->method('getFrontendInput')
            ->willReturn($frontendType);
        $this->columnFactory->expects($this->any())
            ->method('create')
            ->with($this->attribute, $this->contextInterface, $config)
            ->willReturn($this->column);
        $this->getModel()->prepare();
    }

    public function getPrepareDataProvider()
    {
        return [
            ['default',
                [
                    'sortOrder' => 101,
                    'filter' => 'text'
                ]
            ],
            ['select',
                [
                    'sortOrder' => 101,
                    'filter' => 'select'
                ]
            ],
            ['boolean',
                [
                    'sortOrder' => 101,
                    'filter' => 'select'
                ]
            ],
            ['multiselect',
                [
                    'sortOrder' => 101,
                    'filter' => 'select'
                ]
            ],
            ['date',
                [
                    'sortOrder' => 101,
                    'filter' => 'dateRange'
                ]
            ],
            ['someOther',
                [
                    'sortOrder' => 101,
                    'filter' => 'text'
                ]
            ]
        ];
    }
}
