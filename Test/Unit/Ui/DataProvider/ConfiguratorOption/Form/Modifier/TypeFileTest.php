<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 15.01.19
 * Time: 15:49
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Attribute;
use Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier\TypeFile;

class TypeFileTest extends AbstractModifierTest
{
    private $attribute;

    public function setUp()
    {
        parent::setUp();
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }
    /**
     * @return TypeFile
     */
    protected function getModel()
    {
        return new TypeFile(
            $this->searchCriteriaBuilderMock,
            $this->attributeRepositoryMock,
            $this->arrayManager,
            $this->formElementMapperMock,
            $this->eavAttributeFactoryMock,
            $this->dataPersistorMock,
            $this->registryMock,
            $this->eavConfigMock,
            $this->optionTypeSourceMock,
            ['extensions']
        );
    }

    public function testModifyData()
    {
        $this->assertSame([], $this->getModel()->modifyData([]));
        $this->assertSame(['test'], $this->getModel()->modifyData(['test']));
    }

    public function testModifyMeta()
    {
        $configPath = ltrim(TypeFile::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $this->attributeRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($this->searchResultsMock);
        $this->searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->itemMock]);
        $this->itemMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn('extensions');
        $this->itemMock->expects($this->any())
            ->method('getIsRequired')
            ->willReturn(true);
        $this->itemMock->expects($this->any())
            ->method('getFrontendClass')
            ->willReturn('validate-filesize');
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with('extensions')
            ->willReturn($this->attribute);

        $meta = $this->getModel()->modifyMeta(['general' => ['children' => []]]);
        $this->assertArrayHasKey(
            TypeFile::CONTAINER_PREFIX . 'for_' . TypeFile::TYPE,
            $this->arrayManager->get('general/children', $meta)
        );
        $this->assertArrayHasKey(
            'component',
            $this->arrayManager->get(
                'general/children/'
                . TypeFile::CONTAINER_PREFIX . 'for_' . TypeFile::TYPE
                . '/children/extensions/' . $configPath,
                $meta
            )
        );
    }
}
