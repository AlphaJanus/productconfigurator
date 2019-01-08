<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 04.01.19
 * Time: 16:19
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model\Product;

use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption as ResourceModel;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\Collection
    as ResourceCollection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\CollectionFactory;
use Netzexpert\ProductConfigurator\Test\Unit\Model\AbstractModelTest;

class ProductConfiguratorOptionTest extends AbstractModelTest
{

    /** @var Collection | \PHPUnit_Framework_MockObject_MockObject */
    private $collection;

    /** @var CollectionFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $collectionFactory;

    /** @var ResourceModel | \PHPUnit_Framework_MockObject_MockObject */
    private $resource;

    /** @var ResourceCollection | \PHPUnit_Framework_MockObject_MockObject */
    private $resourceCollection;

    /** @var ProductConfiguratorOption */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->collection           = $this->getMock(Collection::class);
        $this->collectionFactory    = $this->getMock(CollectionFactory::class);
        $this->collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        $this->resource             = $this->getMock(ResourceModel::class);
        $this->resourceCollection   = $this->getMock(ResourceCollection::class);
        $this->model                = $this->getModel();
    }

    /**
     * @return ProductConfiguratorOption
     */
    private function getModel()
    {
        return new ProductConfiguratorOption(
            $this->context,
            $this->registry,
            $this->collectionFactory,
            $this->resource,
            $this->resourceCollection,
            []
        );
    }

    public function testGetEnabledOnParentVariants()
    {
        $this->model->setData(ProductConfiguratorOption::ENABLED_ON_PARENT_VARIANTS, '1,2,3');
        $this->assertEquals([1,2,3], $this->model->getEnabledOnParentVariants());
    }

    public function testSetEnabledOnParentVariants()
    {
        $this->model->setEnabledOnParentVariants([3,4,5]);
        $this->assertEquals(
            '3,4,5',
            $this->model->getData(ProductConfiguratorOption::ENABLED_ON_PARENT_VARIANTS)
        );
        $this->assertEquals([3,4,5], $this->model->getEnabledOnParentVariants());
    }

    public function testSetValuesData()
    {
        $this->model->setValuesData(
            [
                0 => 1,
                1 => 2
            ]
        );
        $this->assertEquals(
            [
                0 => 1,
                1 => 2
            ],
            $this->model->getData(ProductConfiguratorOption::VALUES_DATA)
        );
    }

    public function testGetProductId()
    {
        $this->model->setData(ProductConfiguratorOption::PRODUCT_ID, 1);
        $this->assertEquals(1, $this->model->getProductId());
    }

    public function testGetGroupId()
    {
        $this->model->setData(ProductConfiguratorOption::GROUP_ID, 1);
        $this->assertEquals(1, $this->model->getGroupId());
    }

    public function testSetId()
    {
        $this->model->setId(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::ID));
    }

    public function testGetId()
    {
        $this->model->setData(ProductConfiguratorOption::ID, 1);
        $this->assertEquals(1, $this->model->getId());
    }

    public function testSetPosition()
    {
        $this->model->setPosition(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::POSITION));
    }

    public function testGetPosition()
    {
        $this->model->setData(ProductConfiguratorOption::POSITION, 1);
        $this->assertEquals(1, $this->model->getPosition());
    }

    public function testGetConfiguratorOptionId()
    {
        $this->model->setData(ProductConfiguratorOption::CONFIGURATOR_OPTION_ID, 1);
        $this->assertEquals(1, $this->model->getConfiguratorOptionId());
    }

    /**
     * @param string|array $data
     * @param mixed $value
     * @param mixed $expected
     * @dataProvider additionalDataProvider
     */
    public function testSetAdditionalData($data, $value, $expected)
    {
        $this->model->setAdditionalData($data, $value);
        if (is_array($data)) {
            $this->assertTrue(is_array($expected));
            foreach (array_keys($data) as $key) {
                $this->assertEquals($expected[$key], $this->model->getData($key));
            }
            return;
        }
        $this->assertEquals(true, $this->model->hasDataChanges());
        $this->assertEquals($expected, $this->model->getData($data));
    }

    public function testSetProductId()
    {
        $this->model->setProductId(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::PRODUCT_ID));
    }

    public function testSetGroupId()
    {
        $this->model->setGroupId(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::GROUP_ID));
    }

    public function testGetParentOption()
    {
        $this->model->setData(ProductConfiguratorOption::PARENT_OPTION, 1);
        $this->assertEquals(1, $this->model->getParentOption());
    }

    public function testGetValuesData()
    {
        $this->model->setProductId(1)->setConfiguratorOptionId(1);
        $this->collection->expects($this->once())
            ->method('joinProductVariantsData')
            ->willReturnSelf();
        $this->collection->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('setOrder')
            ->with('sort_order', Collection::SORT_ORDER_ASC)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('toArray')
            ->willReturn(['totalRecords' => 0, 'items' => []]);
        $this->assertEquals([], $this->model->getValuesData());
    }

    public function testSetConfiguratorOptionId()
    {
        $this->model->setConfiguratorOptionId(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::CONFIGURATOR_OPTION_ID));
    }

    public function testSetParentOption()
    {
        $this->model->setParentOption(1);
        $this->assertEquals(1, $this->model->getData(ProductConfiguratorOption::PARENT_OPTION));
    }

    public function additionalDataProvider()
    {
        return [
            [
                [
                    'some_value_1' => 1,
                    'some_value_2' => 2,
                ],
                null,
                [
                    'some_value_1' => 1,
                    'some_value_2' => 2,
                ]
            ],
            [
                'some_value',
                1,
                1
            ]
        ];
    }
}
