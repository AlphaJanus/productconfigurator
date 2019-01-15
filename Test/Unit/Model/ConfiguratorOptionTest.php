<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 11:36
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model;

use Netzexpert\ProductConfigurator\Model\ConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOptionVariantsProcessor;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\CollectionFactory
    as VariantsCollectionFactory;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption as ResourceModel;

class ConfiguratorOptionTest extends AbstractModelTest
{

    /** @var VariantsCollectionFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $optionVariantCollectionFactory;

    /** @var ConfiguratorOptionVariantsProcessor \PHPUnit_Framework_MockObject_MockObject */
    private $optionVariantsProcessor;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $optionVariantCollection;

    /** @var ResourceModel | \PHPUnit_Framework_MockObject_MockObject */
    private $resourceModel;

    /** @var ConfiguratorOption | \PHPUnit_Framework_MockObject_MockObject */
    private $model;

    /** @var ConfiguratorOption */
    private $modelMock;

    public function setUp()
    {
        parent::setUp();
        $this->optionVariantCollectionFactory = $this->getMockBuilder(VariantsCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->optionVariantCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'joinProductVariantsData',
                'addFieldToFilter',
                'setOrder'
            ])
            ->getMock();
        $this->optionVariantCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->optionVariantCollection);
        $this->optionVariantCollection->expects($this->any())
            ->method('joinProductVariantsData')
            ->willReturn($this->optionVariantCollection);
        $this->optionVariantCollection->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturn($this->optionVariantCollection);
        $this->optionVariantCollection->expects($this->any())
            ->method('setOrder')
            ->willReturn($this->optionVariantCollection);
        $this->optionVariantsProcessor = $this->getMock(ConfiguratorOptionVariantsProcessor::class);
        $this->resourceModel = $this->getMockBuilder(ResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->modelMock = $this->objectManager->getObject(
            ConfiguratorOption::class,
            [
                $this->context,
                $this->registry,
                $this->optionVariantCollectionFactory,
                $this->optionVariantsProcessor,
                null,
                null,
                []
            ]
        );
        $this->model = new ConfiguratorOption(
            $this->context,
            $this->registry,
            $this->optionVariantCollectionFactory,
            $this->optionVariantsProcessor,
            $this->resourceModel,
            null,
            []
        );
    }

    public function testGetName()
    {
        $this->model->setData(ConfiguratorOption::NAME, 'test');
        $this->assertEquals('test', $this->model->getName());
    }

    public function testGetVariants()
    {
        $this->assertEquals($this->optionVariantCollection, $this->model->getVariants());
    }

    public function testGetType()
    {
        $this->model->setData(ConfiguratorOption::TYPE, 'test');
        $this->assertEquals('test', $this->model->getType());
    }

    public function testGetValues()
    {
        $values = ['one', 'two', 'tree'];
        $this->model->setData(ConfiguratorOption::VALUES, $values);
        $this->assertEquals($values, $this->model->getValues());
    }

    public function testAfterSave()
    {
        $this->assertInstanceOf(
            ConfiguratorOption::class,
            $this->modelMock->afterSave()
        );
    }

    public function testHasVariants()
    {
        $this->model->setType(ConfiguratorOption\Source\OptionType::TYPE_TEXT);
        $this->assertFalse($this->model->hasVariants());
        $this->model->setType(ConfiguratorOption\Source\OptionType::TYPE_SELECT);
        $this->assertTrue($this->model->hasVariants());
    }

    public function testSetType()
    {
        $this->model->setType('test');
        $this->assertEquals('test', $this->model->getType());
    }

    public function testGetTypesWithVariants()
    {
        $this->assertEquals(
            [
                ConfiguratorOption\Source\OptionType::TYPE_SELECT,
                ConfiguratorOption\Source\OptionType::TYPE_RADIO,
                ConfiguratorOption\Source\OptionType::TYPE_IMAGE
            ],
            $this->model->getTypesWithVariants()
        );
    }

    public function testSetValues()
    {
        $values = ['one', 'two', 'tree'];
        $this->model->setValues($values);
        $this->assertEquals($values, $this->model->getValues());
    }

    public function testSetName()
    {
        $this->model->setName('test');
        $this->assertEquals('test', $this->model->getName());
    }

    public function testGetCode()
    {
        $this->model->setData(ConfiguratorOption::CODE, 'test');
        $this->assertEquals('test', $this->model->getCode());
    }

    public function testSetCode()
    {
        $this->model->setCode('test');
        $this->assertEquals('test', $this->model->getCode());
    }

    public function testCreatedAt()
    {
        $date = date("Y-m-d");
        $this->model->setCreatedAt($date);
        $this->assertEquals($date, $this->model->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $date = date("Y-m-d");
        $this->model->setUpdatedAt($date);
        $this->assertEquals($date, $this->model->getUpdatedAt());
    }

    public function testIsDuplicate()
    {
        $isDuplicate = true;
        $this->model->setIsDuplicate($isDuplicate);
        $this->assertEquals($isDuplicate, $this->model->isDuplicate());
    }

    public function testOriginalLinkId()
    {
        $id = 1;
        $this->model->setOriginalLinkId($id);
        $this->assertEquals($id, $this->model->getOriginalLinkId());
    }
}
