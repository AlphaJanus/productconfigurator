<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 05.12.18
 * Time: 15:09
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\DataProvider\Mapper\FormElement;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeSearchResultsInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

abstract class AbstractModifierTest extends TestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    /**
     * @var SearchCriteriaBuilder | MockObject
     */
    protected $searchCriteriaBuilderMock;

    /** @var SearchCriteria | MockObject */
    protected $searchCriteriaMock;

    /** @var ConfiguratorOptionAttributeRepositoryInterface | MockObject */
    protected $attributeRepositoryMock;

    /** @var ConfiguratorOptionAttributeSearchResultsInterface | MockObject */
    protected $searchResultsMock;

    /** @var ConfiguratorOptionAttributeInterface | MockObject */
    protected $itemMock;

    /** @var ArrayManager */
    protected $arrayManager;

    /** @var FormElement | MockObject */
    protected $formElementMapperMock;

    /** @var AttributeFactory | MockObject */
    protected $eavAttributeFactoryMock;

    /** @var DataPersistorInterface | MockObject */
    protected $dataPersistorMock;

    /** @var Registry | MockObject */
    protected $registryMock;

    /** @var Config | MockObject */
    protected $eavConfigMock;

    /** @var OptionType */
    protected $optionTypeSourceMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->searchResultsMock = $this->getMockBuilder(ConfiguratorOptionAttributeSearchResultsInterface::class)
            ->disableOriginalConstructor()
            //->setMethods(['getItems'])
            ->getMock();
        $this->attributeRepositoryMock = $this->getMockBuilder(ConfiguratorOptionAttributeRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemMock = $this->getMockBuilder(ConfiguratorOptionAttributeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->arrayManager= $this->objectManager->getObject(ArrayManager::class);
        $this->formElementMapperMock = $this->getMockBuilder(FormElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eavAttributeFactoryMock = $this->getMockBuilder(AttributeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataPersistorMock = $this->getMockBuilder(DataPersistorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eavConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->optionTypeSourceMock = $this->getMockBuilder(OptionType::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return object
     */
    abstract protected function getModel();


    /**
     * @param null | string $apply
     * @dataProvider providerApplyData
     */
    public function testGetAttributes($apply = null)
    {
        if ($apply===null) {
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('addFilter')
                ->with('apply_to', $apply, 'null');
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('create')
                ->willReturn($this->searchCriteriaMock);
            $this->attributeRepositoryMock->expects($this->once())
                ->method('getList')
                ->with($this->searchCriteriaMock)
                ->willReturn($this->searchResultsMock);
            $this->searchResultsMock->expects($this->once())->method('getItems')->willReturn([$this->itemMock]);
            $expected = [$this->itemMock];
        } else {
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('addFilter')
                ->with('apply_to', $apply);
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('create')
                ->willReturn($this->searchCriteriaMock);
            $this->attributeRepositoryMock->expects($this->once())
                ->method('getList')
                ->with($this->searchCriteriaMock)
                ->willReturn($this->searchResultsMock);
            $this->searchResultsMock->expects($this->once())->method('getItems')->willReturn([$this->itemMock]);
            $expected = [$this->itemMock];
        }

        $this->assertEquals($expected, $this->getModel()->getAttributes($apply));
    }

    public function testSetupAttributeMeta()
    {

    }

    public function providerApplyData()
    {
        return [[null], ['text']];
    }
}
