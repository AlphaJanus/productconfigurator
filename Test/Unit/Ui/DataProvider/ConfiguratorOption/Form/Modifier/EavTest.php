<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 05.12.18
 * Time: 16:33
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier\Eav;

class EavTest extends AbstractModifierTest
{

    protected function getModel()
    {
        return $this->objectManager->getObject(
            Eav::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'attributeRepository'   => $this->attributeRepositoryMock,
                'arrayManager'          => $this->arrayManagerMock,
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

    }

    public function testModifyData()
    {

    }
}
