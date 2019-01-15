<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 14.01.19
 * Time: 17:20
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model\ConfiguratorOption;

use Magento\Framework\Exception\CouldNotSaveException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterfaceFactory;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Copier;
use Netzexpert\ProductConfigurator\Test\Unit\Model\AbstractModelTest;

class CopierTest extends AbstractModelTest
{
    /** @var ConfiguratorOptionInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $option;

    /** @var ConfiguratorOptionInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject */
    private $optionFactory;

    /** @var ConfiguratorOptionRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $optionRepository;

    /** @var CouldNotSaveException | \PHPUnit_Framework_MockObject_MockObject */
    private $couldNotSaveException;

    /** @var Copier */
    private $copier;

    public function setUp()
    {
        parent::setUp();
        $this->option                   = $this->getMock(ConfiguratorOptionInterface::class);
        $this->optionFactory            = $this->getMock(ConfiguratorOptionInterfaceFactory::class);
        $this->optionRepository         = $this->getMock(ConfiguratorOptionRepositoryInterface::class);
        $this->couldNotSaveException    = $this->getMock(CouldNotSaveException::class);

        $this->optionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->option);

        $this->copier = new Copier(
            $this->optionFactory,
            $this->optionRepository
        );
    }

    /**
     * @param $origCode string
     * @param $duplicateCode string
     * @dataProvider copyDataProvider
     */
    public function testCopy($origCode, $duplicateCode)
    {
        $this->option->expects($this->once())
            ->method('getData')
            ->willReturn(['id' => 1]);
        $this->option->expects($this->once())
            ->method('getCode')
            ->willReturn($origCode);
        $this->option->expects($this->once())
            ->method('setCode')
            ->with($duplicateCode);
        $this->assertEquals($this->option, $this->copier->copy($this->option));
    }

    public function testCopyException()
    {
        $this->option->expects($this->once())
            ->method('getData')
            ->willReturn(['id' => 1]);
        $this->option->expects($this->exactly(2))
            ->method('setCode');
        $this->optionRepository->expects($this->at(0))
            ->method('save')
            ->with($this->option)
            ->willThrowException($this->couldNotSaveException);
        $this->optionRepository->expects($this->at(1))
            ->method('save')
            ->with($this->option)
            ->willReturn($this->option);
        $this->assertEquals($this->option, $this->copier->copy($this->option));
    }

    public function copyDataProvider()
    {
        return [
            ['test', 'test-1'],
            ['test-1', 'test-2']
        ];
    }
}
