<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 20.12.18
 * Time: 17:55
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Filter\Template;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Netzexpert\ProductConfigurator\Ui\Component\Listing\Columns\Description;
use PHPUnit\Framework\TestCase;

class DescriptionTest extends TestCase
{

    /** @var ObjectManager */
    private $objectManager;

    /** @var ContextInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $contextInterface;

    /** @var UiComponentFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $uiComponentFactory;


    /** @var FilterProvider | \PHPUnit_Framework_MockObject_MockObject */
    private $filterProvider;

    /** @var Template | \PHPUnit_Framework_MockObject_MockObject */
    private $templateFilter;

    /** @var \Exception | \PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /** @var Description | object */
    private $model;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextInterface = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterProvider = $this->getMockBuilder(FilterProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPageFilter'])
            ->getMock();
        $this->templateFilter = $this->getMockBuilder(Template::class)
            ->disableOriginalConstructor()
            ->setMethods(['filter'])
            ->getMock();
        $this->exception = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Description(
            $this->contextInterface,
            $this->uiComponentFactory,
            $this->filterProvider,
            [],
            []
        );
    }

    public function testPrepareDataSource()
    {
        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'description' => 'test'
                    ],
                    [
                        'description' => 'test1'
                    ]
                ],
            ],
        ];
        $this->model->setData('name', 'description');
        $this->filterProvider->expects($this->any())
            ->method('getPageFilter')
            ->willReturn($this->templateFilter);
        $this->templateFilter->expects($this->any())
            ->method('filter')
            ->with('test')
            ->willReturnSelf();
        $this->templateFilter->expects($this->any())
            ->method('filter')
            ->with('test1')
            ->willThrowException($this->exception);
        $this->assertEquals($dataSource, $this->model->prepareDataSource($dataSource));
    }
}
