<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 10:36
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model\Quote\Item;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Netzexpert\ProductConfigurator\Model\Quote\Item\ConfiguratorItemOptionValue;
use Netzexpert\ProductConfigurator\Test\Unit\Model\AbstractModelTest;
use PHPUnit\Framework\MockObject\MockObject;

class ConfiguratorItemOptionValueTest extends AbstractModelTest
{

    /** @var ExtensionAttributesFactory | MockObject */
    private $extensionAttributesFactory;

    /** @var AttributeValueFactory | MockObject */
    private $customAttributeFactory;

    /** @var ConfiguratorItemOptionValue */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->extensionAttributesFactory = $this->getMock(ExtensionAttributesFactory::class);
        $this->customAttributeFactory = $this->getMock(AttributeValueFactory::class);
        $this->model = $this->objectManager->getObject(
            ConfiguratorItemOptionValue::class,
            [
                $this->context,
                $this->registry,
                $this->extensionAttributesFactory,
                $this->customAttributeFactory,
                null,
                null,
                []
            ]

        );
    }



    public function testSetOptionTitle()
    {
        $this->model->setOptionTitle('test');
        $this->assertEquals('test', $this->model->getData(ConfiguratorItemOptionValue::OPTION_TITLE));
    }

    public function testGetOptionId()
    {
        $this->model->setOptionId('10');
        $this->assertEquals('10', $this->model->getOptionId());
    }

    public function testGetOptionValue()
    {
        $this->model->setOptionValue('test');
        $this->assertEquals('test', $this->model->getOptionValue());
    }

    public function testSetOptionId()
    {
        $this->model->setOptionId('10');
        $this->assertEquals('10', $this->model->getData(ConfiguratorItemOptionValue::OPTION_ID));
    }

    public function testGetOptionTitle()
    {
        $this->model->setOptionTitle('test');
        $this->assertEquals('test', $this->model->getOptionTitle());
    }

    public function testSetOptionValue()
    {
        $this->model->setOptionValue('test');
        $this->assertEquals('test', $this->model->getData(ConfiguratorItemOptionValue::OPTION_VALUE));
    }
}
