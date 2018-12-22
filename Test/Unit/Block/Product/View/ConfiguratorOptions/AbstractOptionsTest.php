<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 21.12.18
 * Time: 11:03
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Product\View\ConfiguratorOptions;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\Template;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionSearchResultInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractOptionsTest
 * @package Netzexpert\ProductConfigurator\Test\Unit\Block\Product\View\ConfiguratorOptions
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AbstractOptionsTest extends TestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var DataObject | \PHPUnit_Framework_MockObject_MockObject */
    protected $dataObject;

    /** @var Context | \PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var ConfiguratorOptionRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $productConfiguratorOptionRepository;

    /** @var SearchCriteriaBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $searchCriteriaBuilder;

    /** @var SearchCriteria | \PHPUnit_Framework_MockObject_MockObject */
    protected $searchCriteria;

    /** @var ProductConfiguratorOptionSearchResultInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $searchResult;

    /** @var FilterProvider | \PHPUnit_Framework_MockObject_MockObject */
    protected $filterProvider;

    /** @var Template | \PHPUnit_Framework_MockObject_MockObject */
    private $templateFilter;

    /** @var ConfiguratorOptionInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $option;

    /** @var ProductConfiguratorOptionInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $productOption;

    /** @var ProductInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    /** @var \Exception | \PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /** @var LocalizedException | \PHPUnit_Framework_MockObject_MockObject */
    protected $localisedException;

    /** @var NoSuchEntityException | \PHPUnit_Framework_MockObject_MockObject */
    protected $noSuchEntityException;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->dataObject = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuratorOptionRepository =
            $this->getMockBuilder(ConfiguratorOptionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productConfiguratorOptionRepository =
            $this->getMockBuilder(ProductConfiguratorOptionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->willReturn($this->searchCriteria);
        $this->filterProvider = $this->getMockBuilder(FilterProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->templateFilter = $this->getMockBuilder(Template::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->option = $this->getMockBuilder(ConfiguratorOptionInterface::class)
            ->setMethods(['getValues'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productOption = $this->getMockBuilder(ProductConfiguratorOptionInterface::class)
            ->setMethods(['getParentOption'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->exception = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->localisedException = $this->getMockBuilder(LocalizedException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->noSuchEntityException = $this->getMockBuilder(NoSuchEntityException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->any())
            ->method('getLogger')
            ->willReturn($this->createMock(LoggerInterface::class));
        $this->product = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getPreconfiguredValues'])
            ->getMockForAbstractClass();
        $this->searchResult = $this->getMockBuilder(ProductConfiguratorOptionSearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productOption->expects($this->any())
            ->method('getParentOption')
            ->willReturn(10);
        $this->product->expects($this->any())
            ->method('getId')
            ->willReturn(1);
    }

    /**
     * @param string $class
     * @return AbstractOptions | object
     */
    protected function getModel($class = AbstractOptions::class)
    {
        return $this->objectManager->getObject($class, [
            'context' => $this->context,
            'configuratorOptionRepository'          => $this->configuratorOptionRepository,
            'productConfiguratorOptionRepository'   => $this->productConfiguratorOptionRepository,
            'searchCriteriaBuilder'                 => $this->searchCriteriaBuilder,
            'filterProvider'                        => $this->filterProvider,
            []
        ]);
    }

    /**
     * @param int $count
     * @param array $valuesData
     * @dataProvider testGetAvailableOptionsCountDataProvider
     */
    public function testGetAvailableOptionsCount($count, $valuesData)
    {
        /** @var AbstractOptions | \PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockBuilder(AbstractOptions::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValuesData','getParentOptionDefaultValue'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getValuesData')
            ->willReturn($valuesData);
        $mock->expects($this->any())
            ->method('getParentOptionDefaultValue')
            ->willReturn(43);
        $this->assertEquals($count, $mock->getAvailableOptionsCount());
    }

    public function testSetProduct()
    {
        $model = $this->getModel();
        $model->setProduct($this->product);
        $this->assertEquals($this->product, $model->getProduct());
    }

    public function testGetOptionDescription()
    {
        $this->productOption->expects($this->any())
            ->method('getData')
            ->with('description')
            ->willReturn('test');
        /** @var AbstractOptions | \PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getModel()->setOption($this->productOption);
        $this->filterProvider->expects($this->any())
            ->method('getPageFilter')
            ->willReturn($this->templateFilter);
        $this->templateFilter->expects($this->any())
            ->method('filter')
            ->with('test')
            ->willReturn('test');
        $this->assertEquals($this->productOption->getData('description'), $model->getOptionDescription());
    }

    public function testGetOptionDescriptionWithException()
    {
        $this->productOption->expects($this->any())
            ->method('getData')
            ->with('description')
            ->willReturn('test1');
        $model = $this->getModel()->setOption($this->productOption);
        $this->filterProvider->expects($this->any())
            ->method('getPageFilter')
            ->willReturn($this->templateFilter);
        $this->templateFilter->expects($this->any())
            ->method('filter')
            ->with('test1')
            ->willThrowException($this->exception);
        $this->assertEquals('test1', $model->getOptionDescription());
    }

    public function testGetValuesData()
    {
        $this->productOption->expects($this->once())
            ->method('getValuesData')
            ->willReturn(['test']);
        $this->assertEquals(['test'], $this->getModel()->setOption($this->productOption)->getValuesData());
    }

    public function testSetOption()
    {
        $model = $this->getModel();
        $model->setOption($this->productOption);
        $this->assertEquals($this->productOption, $model->getOption());
    }

    /**
     * @param int $preconfigured_value
     * @param int $expected
     * @dataProvider testGetParentOptionDefaultValueDataProvider
     */
    public function testGetParentOptionDefaultValue($preconfigured_value, $expected)
    {
        $this->productConfiguratorOptionRepository->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteria)
            ->willReturn($this->searchResult);
        $this->searchResult->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(1);
        $this->searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->option]);
        $this->option->expects($this->any())
            ->method('getId')
            ->willReturn(10);
        $this->product->expects($this->once())
            ->method('getPreconfiguredValues')
            ->willReturn($this->dataObject);
        $this->dataObject->expects($this->once())
            ->method('getData')
            ->with('configurator_options/10')
            ->willReturn($preconfigured_value);

        $this->configuratorOptionRepository->expects($this->any())
            ->method('get')
            ->with('10')
            ->willReturn($this->option);
        $this->option->expects($this->any())
            ->method('getValues')
            ->willReturn([
                ['value_id' => 43, 'is_default' => false],
                ['value_id' => 59, 'is_default' => true]
            ]);

        $this->assertEquals(
            $expected,
            $this->getModel()->setOption($this->productOption)
            ->setProduct($this->product)
            ->getParentOptionDefaultValue()
        );
    }

    public function testGetParentOptionDefaultValueWithException()
    {
        $this->productConfiguratorOptionRepository->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteria)
            ->willThrowException($this->localisedException);
        $this->configuratorOptionRepository->expects($this->once())
            ->method('get')
            ->with('10')
            ->willThrowException($this->noSuchEntityException);

        $this->assertEquals(
            null,
            $this->getModel()->setOption($this->productOption)
                ->setProduct($this->product)
                ->getParentOptionDefaultValue()
        );
    }

    /**
     * @return array
     */
    public function testGetAvailableOptionsCountDataProvider()
    {
        return [
            [
                'count' => 2,
                'values' => [
                    [
                        'value_id' => "44",
                        'allowed_variants' => "43",
                        'is_dependent' => true
                    ],
                    [
                        'value_id' => "45",
                        'allowed_variants' => "43,59",
                        'is_dependent' => true
                    ]
                ],
            ],[
                'count' => 1,
                'values' => [
                    [
                        'value_id' => "44",
                        'allowed_variants' => "43",
                        'is_dependent' => true
                    ],
                    [
                        'value_id' => "45",
                        'allowed_variants' => "59",
                        'is_dependent' => true
                    ]
                ],
            ],
            [
                'count' => 2,
                'values' => [
                    [
                        'value_id' => "44",
                        'allowed_variants' => "43",
                        'is_dependent' => false
                    ],
                    [
                        'value_id' => "45",
                        'allowed_variants' => "59",
                        'is_dependent' => false
                    ]
                ],
            ],
            [
                'count' => 0,
                'values' => [
                    [
                        'value_id' => "44",
                        'allowed_variants' => "59",
                        'is_dependent' => true
                    ],
                    [
                        'value_id' => "45",
                        'allowed_variants' => "59",
                        'is_dependent' => true
                    ]
                ],
            ],
        ];
    }

    public function testGetParentOptionDefaultValueDataProvider()
    {
        return [
            [
                'preconfigured_value'   => 43,
                'expected'              => 43
            ],
            [
                'preconfigured_value'   => null,
                'expected'              => 59
            ]
        ];
    }
}
