<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 05.12.18
 * Time: 15:06
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier\Description;

class DescriptionTest extends AbstractModifierTest
{

    /**
     * @return object
     */
    protected function getModel()
    {
        return $this->objectManager->getObject(
            Description::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'attributeRepository'   => $this->attributeRepositoryMock,
                'arrayManager'          => $this->arrayManager,
                'formElementMapper'     => $this->formElementMapperMock,
                'eavAttributeFactory'   => $this->eavAttributeFactoryMock,
                'dataPersistor'         => $this->dataPersistorMock,
                'registry'              => $this->registryMock,
                'eavConfig'             => $this->eavConfigMock,
                'optionTypeSource'      => $this->optionTypeSourceMock
            ]
        );
    }


    public function testModifyMeta()
    {
        $meta = [
            'general' => [
                'children' => [
                    'container_description' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'sortOrder' => 10,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $descriptionPath = 'general/children/container_description/arguments/data/config';
        $configPath = ltrim($descriptionPath, ArrayManager::DEFAULT_PATH_DELIMITER);

        $meta = $this->getModel()->modifyMeta($meta);
        $this->assertArrayHasKey('component', $this->arrayManager->get($configPath, $meta));
    }

    public function testModifyData()
    {
        $this->assertSame([], $this->getModel()->modifyData([]));
    }
}
