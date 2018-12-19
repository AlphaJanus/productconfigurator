<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 17:28
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOptionRepository;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Variant;
use Netzexpert\ProductConfigurator\Model\Quote\Item\ConfiguratorItemOptionValue;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorItemOptionValueInterfaceFactory;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionRepository;
use Netzexpert\ProductConfigurator\Model\ProductOptionProcessor;
use Psr\Log\LoggerInterface;


class ProductOptionProcessorTest extends AbstractModelTest
{

    /** @var DataObjectFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $objectFactory;

    /** @var DataObject | \PHPUnit_Framework_MockObject_MockObject */
    private $dataObject;

    /** @var ConfiguratorItemOptionValueInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $optionValueInterfaceFactory;

    /** @var ConfiguratorItemOptionValue | \PHPUnit_Framework_MockObject_MockObject*/
    private $optionValueInterface;

    /** @var ConfiguratorOptionRepository | \PHPUnit_Framework_MockObject_MockObject */
    private $optionRepository;

    /** @var ConfiguratorOptionInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $option;

    /** @var ProductConfiguratorOptionRepository | \PHPUnit_Framework_MockObject_MockObject */
    private $productConfiguratorOptionRepository;

    /** @var ProductConfiguratorOption | \PHPUnit_Framework_MockObject_MockObject */
    private $productConfiguratorOption;

    /** @var ConfiguratorOptionVariantRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $optionVariantRepository;

    /** @var Variant | \PHPUnit_Framework_MockObject_MockObject */
    private $optionVariant;

    /** @var NoSuchEntityException | \PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /** @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var ProductOptionProcessor \PHPUnit_Framework_MockObject_MockObject */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectFactory = $this->getMock(DataObjectFactory::class, ['create']);
        $this->dataObject = $this->getMock(DataObject::class, ['getData']);
        $this->objectFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->dataObject);
        $this->optionValueInterfaceFactory =
            $this->getMock(ConfiguratorItemOptionValueInterfaceFactory::class, ['create']);
        $this->optionValueInterface = $this->getMock(
                ConfiguratorItemOptionValue::class,
                ['setOptionId', 'setOptionTitle', 'setOptionValue']
            );
        $this->optionRepository = $this->getMock(ConfiguratorOptionRepository::class, ['get']);
        $this->option = $this->getMock(ConfiguratorOptionInterface::class);
        $this->productConfiguratorOptionRepository =
            $this->getMock(ProductConfiguratorOptionRepository::class, ['get']);
        $this->productConfiguratorOption = $this->getMock(ProductConfiguratorOption::class, ['getName']);
        $this->optionVariantRepository = $this->getMock(ConfiguratorOptionVariantRepositoryInterface::class);
        $this->optionVariant = $this->getMock(Variant::class);
        $this->exception = $this->getMock(NoSuchEntityException::class, ['getMessage']);
        $this->logger = $this->getMock(LoggerInterface::class);

        $this->model = new ProductOptionProcessor(
            $this->objectFactory,
            $this->optionValueInterfaceFactory,
            $this->optionRepository,
            $this->productConfiguratorOptionRepository,
            $this->optionVariantRepository,
            $this->logger
        );
    }

    public function testConvertToBuyRequest()
    {
        /** @var ProductOptionInterface | \PHPUnit_Framework_MockObject_MockObject $productOptionInterface */
        $productOptionInterface = $this->getMock(ProductOptionInterface::class);
        $this->objectFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->dataObject);
        $this->assertEquals($this->dataObject, $this->model->convertToBuyRequest($productOptionInterface));
    }


    /**
     * @param $options
     * @dataProvider dataProviderConvertToProductOption
     */
    public function testConvertToProductOption(
        $options
    ) {
        $this->dataObject->expects($this->any())
            ->method('getData')
            ->with('configurator_options')
            ->willReturn($options);
        if (!empty($options) && is_array($options)) {
            foreach ($options as $optionId => $optionValue) {
                $this->optionValueInterfaceFactory->expects($this->any())
                    ->method('create')
                    ->willReturn($this->optionValueInterface);
                $this->productConfiguratorOptionRepository->expects($this->any())
                    ->method('get')
                    ->willReturn($this->productConfiguratorOption);
                if ($optionId == 3) {
                    $this->productConfiguratorOptionRepository->expects($this->any())
                        ->method('get')
                        ->with($optionId)
                        ->willThrowException($this->exception);
                    $this->optionValueInterface->expects($this->any())
                        ->method('setOptionId')
                        ->with($optionId)
                        ->willReturnSelf();
                    $this->optionValueInterface->expects($this->any())
                        ->method('setOptionValue')
                        ->with('not exist')
                        ->willReturnSelf();
                } else {
                    $this->optionRepository->expects($this->any())
                        ->method('get')
                        ->willReturn($this->option);
                    $this->option->expects($this->any())
                        ->method('getName')
                        ->willReturn('test');
                    $this->optionValueInterface->expects($this->any())
                        ->method('setOptionId')
                        ->with($optionId)
                        ->willReturnSelf();
                    $this->optionValueInterface->expects($this->any())
                        ->method('setOptionTitle')
                        ->with('test')
                        ->willReturnSelf();

                    if ($optionId == 1) {
                        $this->option->expects($this->any())
                            ->method('hasVariants')
                            ->willReturn(true);
                        $this->optionVariantRepository->expects($this->any())
                            ->method('get')
                            ->with(10)
                            ->willReturn($this->optionVariant);
                        $this->optionVariant->expects($this->any())
                            ->method('getTitle')
                            ->willReturn('testTitle');
                        $this->optionValueInterface->expects($this->any())
                            ->method('setOptionValue')
                            ->with('testTitle')
                            ->willReturnSelf();
                    } else {
                        $this->option->expects($this->any())
                            ->method('hasVariants')
                            ->willReturn(false);
                        $this->optionValueInterface->expects($this->any())
                            ->method('setOptionValue')
                            ->with('value')
                            ->willReturnSelf();
                    }
                }
            }

            $this->assertArrayHasKey(
                'configurator_options',
                $this->model->convertToProductOption($this->dataObject)
            );
        } else {
            $this->assertEmpty($this->model->convertToProductOption($this->dataObject));
        }
    }

    public function dataProviderConvertToProductOption()
    {
        return [
            [
                'configurator_options' => [
                    1 => 10,
                ],
            ],
            [
                'configurator_options' => [
                    2 => 'value',
                ],
            ],
            [
                'configurator_options' => [
                    3 => 'not exist',
                ],
            ],
            [
                'configurator_options' => []
            ],
            [
                'configurator_options' => 'is not array'
            ],
        ];
    }
}
