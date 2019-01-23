<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 15.01.19
 * Time: 15:49
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier\TypeFile;

class TypeFileTest extends AbstractModifierTest
{

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
            []
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

        $meta = $this->getModel()->modifyMeta(['general' => ['children' => []]]);

        $this->assertArrayHasKey(
            TypeFile::CONTAINER_PREFIX . 'for_' . TypeFile::TYPE,
            $this->arrayManager->get('general/children', $meta)
        );
    }
}
