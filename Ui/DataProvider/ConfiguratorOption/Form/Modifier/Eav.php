<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 12.04.18
 * Time: 12:39
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;

class Eav extends AbstractModifier
{

    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    const SORT_ORDER_MULTIPLIER = 10;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /** @var ConfiguratorOptionAttributeRepositoryInterface  */
    private $attributeRepository;

    /** @var ArrayManager  */
    private $arrayManager;

    /** @var FormElementMapper  */
    private $formElementMapper;

    /** @var EavAttributeFactory  */
    private $eavAttributeFactory;

    /** @var DataPersistorInterface */
    private $dataPersistor;

    /**
     * @var Attribute[]
     */
    private $attributes = [];

    /** @var Registry  */
    private $registry;

    /** @var array */
    private $attributesToDisable;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConfiguratorOptionAttributeRepositoryInterface $attributeRepository,
        ArrayManager $arrayManager,
        FormElementMapper $formElementMapper,
        EavAttributeFactory $eavAttributeFactory,
        DataPersistorInterface $dataPersistor,
        Registry $registry,
        $attributesToDisable = []
    ) {
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->attributeRepository      = $attributeRepository;
        $this->arrayManager             = $arrayManager;
        $this->formElementMapper        = $formElementMapper;
        $this->eavAttributeFactory      = $eavAttributeFactory;
        $this->dataPersistor            = $dataPersistor;
        $this->registry                 = $registry;
        $this->attributesToDisable      = $attributesToDisable;
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
        $meta['general']['arguments']['data']['config'] = [
            'componentType' => Fieldset::NAME,
            'label'         => __('General'),
            'collapsible'   => false,
            'dataScope'     => 'data.option',
            'sortOrder'     => 0

        ];
        /** @var Attribute $attribute */
        foreach ($this->getAttributes() as $attribute) {
            $containerMeta = $this->arrayManager->set(
                'arguments/data/config',
                [],
                [
                    'formElement' => 'container',
                    'componentType' => 'container',
                    'breakLine' => false,
                    'label' => $attribute->getDefaultFrontendLabel(),
                    'required' => $attribute->getIsRequired(),
                ]
            );
            $containerMeta['children'][$attribute->getAttributeCode()] = $this->setupAttributeMeta($attribute);
            $meta['general']['children'][static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $containerMeta;
        }
        return $meta;
    }

    private function getAttributes()
    {
        if (!$this->attributes) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $this->attributes = $this->attributeRepository->getList($searchCriteria)->getItems();
        }
        return $this->attributes;
    }

    public function setupAttributeMeta(Attribute $attribute, $sortOrder = 0)
    {
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

        $meta = $this->arrayManager->set($configPath, [], [
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
            'visible' => true,
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote(),
            'default' => null,
            'label' => $attribute->getDefaultFrontendLabel(),
            'code' => $attribute->getAttributeCode(),
            'source' => 'general',
            'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER,
        ]);

        if($attribute->getIsRequired()){
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'validation' => array('required-entry' => true),
            ]);
        }

        $attributeModel = $this->getAttributeModel($attribute);
        if ($attributeModel->usesSource()) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'options' => $attributeModel->getSource()->getAllOptions(),
            ]);
        }

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'componentType' => Field::NAME,
            ]);
        }

        if (in_array($attribute->getAttributeCode(), $this->attributesToDisable)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'disabled' => true,
            ]);
        }


        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $meta = $this->customizeCheckbox($attribute, $meta);
                break;
            /*case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            case 'price':
                $meta = $this->customizePriceAttribute($attribute, $meta);
                break;
            case 'gallery':
                // Gallery attribute is being handled by "Images And Videos" section
                $meta = [];
                break;*/
        }

        return $meta;
    }

    /**
     * Retrieve form element
     *
     * @param string $value
     * @return mixed
     */
    private function getFormElementsMapValue($value)
    {
        $valueMap = $this->formElementMapper->getMappings();

        return isset($valueMap[$value]) ? $valueMap[$value] : $value;
    }

    private function getAttributeModel($attribute)
    {
        return $this->eavAttributeFactory->create()->load($attribute->getAttributeId());
    }

    /**
     * Customize checkboxes
     *
     * @param Attribute $attribute
     * @param array $meta
     * @return array
     */
    private function customizeCheckbox(Attribute $attribute, array $meta)
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $config = [
                'dataType' => Form\Element\DataType\Number::NAME,
                'formElement' => Form\Element\Checkbox::NAME,
                'componentType' => Form\Field::NAME,
                'prefer' => 'toggle',
                'valueMap' => [
                    'true' => '1',
                    'false' => '0'
                ],
            ];
            $meta = $this->arrayManager->merge('arguments/data/config', $meta, $config);
        }

        return $meta;
    }
}
