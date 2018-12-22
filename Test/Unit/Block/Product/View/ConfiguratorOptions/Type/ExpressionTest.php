<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 22.12.18
 * Time: 14:21
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Block\Product\View\ConfiguratorOptions\Type;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type\Expression;
use Netzexpert\ProductConfigurator\Test\Unit\Block\Product\View\ConfiguratorOptions\AbstractOptionsTest;

class ExpressionTest extends AbstractOptionsTest
{

    /** @var Json */
    private $json;

    /** @var ProductExtensionInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $productExtension;

    /** @var ProductExtensionFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $extensionFactory;

    public function setUp()
    {
        $this->json = new \Magento\Framework\Serialize\Serializer\Json();
        $this->productExtension = $this->getMockBuilder(ProductExtensionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfiguratorOptions'])
            ->getMockForAbstractClass();
        $this->extensionFactory = $this->getMockBuilder(ProductExtensionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->productExtension);
        parent::setUp();
    }

    /**
     * @param string $class
     * @return Expression|object
     */
    public function getModel($class = Expression::class)
    {
        return $this->objectManager->getObject($class, [
            'context' => $this->context,
            'configuratorOptionRepository'          => $this->configuratorOptionRepository,
            'productConfiguratorOptionRepository'   => $this->productConfiguratorOptionRepository,
            'searchCriteriaBuilder'                 => $this->searchCriteriaBuilder,
            'filterProvider'                        => $this->filterProvider,
            'json'                                  => $this->json,
            'extensionFactory'                      => $this->extensionFactory,
            []
        ]);
    }

    public function testGetConfiguratorOptions()
    {
        $this->productExtension->expects($this->once())
            ->method('getConfiguratorOptions')
            ->willReturn(
                [
                    3 => [
                        'group_name'    => 'test',
                        'options'       => [$this->productOption]
                    ]
                ]
            );

        $this->productOption->expects($this->any())
            ->method('getConfiguratorOptionId')
            ->willReturn(1);
        $this->configuratorOptionRepository->expects($this->any())
            ->method('get')
            ->willReturn($this->option);
        $this->assertEquals(
            [
                3 => [
                    'group_name'    => 'test',
                    'options'       => [$this->productOption]
                ]
            ],
            $this->getModel()->setProduct($this->product)->getConfiguratorOptions()
        );
    }

    public function testGetConfiguratorOptionsEmptyResult()
    {
        $this->productExtension->expects($this->once())
            ->method('getConfiguratorOptions')
            ->willReturn(null);
        $this->assertEquals(
            null,
            $this->getModel()->setProduct($this->product)->getConfiguratorOptions()
        );
    }

    public function testGetConfiguratorOptionsWithException()
    {
        $this->productExtension->expects($this->once())
            ->method('getConfiguratorOptions')
            ->willReturn(
                [
                    3 => [
                        'group_name'    => 'test',
                        'options'       => [$this->productOption]
                    ]
                ]
            );

        $this->productOption->expects($this->any())
            ->method('getConfiguratorOptionId')
            ->willReturn(1);
        $this->configuratorOptionRepository->expects($this->any())
            ->method('get')
            ->willThrowException($this->noSuchEntityException);
        $this->assertEquals(
            [
                3 => [
                    'group_name'    => 'test',
                    'options'       => [$this->productOption]
                ]
            ],
            $this->getModel()->setProduct($this->product)->getConfiguratorOptions()
        );
    }

    /**
     * @param string $id
     * @param array $data
     * @param array|null $valuesData
     * @param string $espected
     * @dataProvider testGetDependencyJsonConfigDataProvider
     */
    public function testGetDependencyJsonConfig($id, $data, $valuesData, $espected)
    {
        $this->productExtension->expects($this->once())
            ->method('getConfiguratorOptions')
            ->willReturn(
                [
                    3 => [
                        'group_name'    => 'test',
                        'options'       => [$this->productOption]
                    ]
                ]
            );

        $this->productOption->expects($this->any())
            ->method('getConfiguratorOptionId')
            ->willReturn(1);
        $this->configuratorOptionRepository->expects($this->any())
            ->method('get')
            ->willReturn($this->option);
        $this->productOption->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $this->productOption->expects($this->any())
            ->method('getValuesData')
            ->willReturn($valuesData);
        $this->productOption->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $this->assertEquals(
            $espected,
            $this->getModel()->setProduct($this->product)->getDependencyJsonConfig()
        );
    }

    public function testGetDependencyJsonConfigDataProvider()
    {
        return [
            [
                'id' => 1,
                'data' => [
                    'option_id' => 1,
                    'name' => 'Test 1',
                    'values_data' => [
                        'value_id' => '1',
                        'tilte' => 'test value'
                    ],
                ],
                'values_data' => [
                    'value_id' => '1',
                    'tilte' => 'test value'
                ],
                'expected' => '{"1":{"option_id":1,"name":"Test 1","values":{"value_id":"1","tilte":"test value"}}}'
            ],
            [
                'id' => 2,
                'data' => [
                    'option_id' => 2,
                    'name' => 'Test 2'
                ],
                'values_data' => null,
                'expected' => '{"2":{"option_id":2,"name":"Test 2","values":[]}}'
            ]
        ];
    }
}
