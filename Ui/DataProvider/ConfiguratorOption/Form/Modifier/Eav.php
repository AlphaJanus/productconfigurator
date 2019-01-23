<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 12.04.18
 * Time: 12:39
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Attribute;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

class Eav extends AbstractModifier
{
    /** @var DataPersistorInterface */
    private $dataPersistor;

    /** @var Registry  */
    private $registry;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConfiguratorOptionAttributeRepositoryInterface $attributeRepository,
        ArrayManager $arrayManager,
        FormElementMapper $formElementMapper,
        EavAttributeFactory $eavAttributeFactory,
        DataPersistorInterface $dataPersistor,
        Registry $registry,
        Config $eavConfig,
        OptionType $optionTypeSource,
        array $attributesToDisable = []
    ) {
        parent::__construct(
            $searchCriteriaBuilder,
            $attributeRepository,
            $arrayManager,
            $formElementMapper,
            $eavAttributeFactory,
            $dataPersistor,
            $registry,
            $eavConfig,
            $optionTypeSource,
            $attributesToDisable
        );
        $this->arrayManager     = $arrayManager;
        $this->dataPersistor    = $dataPersistor;
        $this->registry         = $registry;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $option = $this->registry->registry('configurator_option');
        $optionId = null;
        $optionData = [];
        if ($option) {
            $optionId = $option->getId();
            $optionData = $option->getData();
        }
        if (!$optionId && $this->dataPersistor->get('configurator_option')) {
            return $this->resolvePersistentData($data);
        }
        foreach ($optionData as $key => $value) {
            $data[$optionId]['option'][$key] = $value;
        }
        return $data;
    }

    /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('configurator_option');
        $this->dataPersistor->clear('configurator_option');
        $option = $this->registry->registry('configurator_option');
        $optionId = null;
        if ($option) {
            $optionId = $option->getId();
        }

        if (empty($data[$optionId]['option'])) {
            $data[$optionId]['option'] = [];
        }

        $data[$optionId] = array_replace_recursive(
            $data[$optionId]['option'],
            $persistentData
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $meta ['general'] = $this->arrayManager->set(
            $configPath,
            [],
            [
                'componentType' => Fieldset::NAME,
                'label'         => __('General'),
                'collapsible'   => false,
                'dataScope'     => 'data.option',
                'sortOrder'     => 0

            ]
        );
        $containerCount = 0;
        /** @var Attribute $attribute */
        foreach ($this->getAttributes() as $attribute) {
            $containerMeta = $this->arrayManager->set(
                $configPath,
                [],
                [
                    'formElement'   => Container::NAME,
                    'componentType' => Container::NAME,
                    'breakLine'     => false,
                    'label'         => $attribute->getDefaultFrontendLabel(),
                    'required'      => $attribute->getIsRequired(),
                    'sortOrder'     => $attribute->getSortOrder()
                ]
            );
            $containerMeta['children'][$attribute->getAttributeCode()] = $this->setupAttributeMeta($attribute);
            $meta['general']['children'][static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $containerMeta;
            $containerCount++;
        }

        $attributeMeta = $this->arrayManager->set($configPath, [], [
            'dataType'      => 'text',
            'formElement'   => Input::NAME,
            'componentType' => Field::NAME,
            'visible'       => true,
            'required'      => true,
            'default'       => null,
            'label'         => __('Code'),
            'code'          => 'code',
            'source'        => 'general',
            'sortOrder'     => 15,
            'validation' => ['required-entry' => true]
        ]);

        $container[static::CONTAINER_PREFIX . 'code'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'   => Container::NAME,
                        'componentType' => Container::NAME,
                        'breakLine'     => false,
                        'label'         => $attribute->getDefaultFrontendLabel(),
                        'required'      => $attribute->getIsRequired(),
                        'sortOrder'     => 15
                    ],
                ],
            ],
            'children' => [
                'code' => $attributeMeta
            ]
        ];
        $meta = $this->arrayManager->merge(
            'general/children',
            $meta,
            $container
        );

        return $meta;
    }
}
