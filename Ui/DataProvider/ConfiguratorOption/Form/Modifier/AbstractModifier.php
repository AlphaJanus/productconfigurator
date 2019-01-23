<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 23.04.18
 * Time: 9:42
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

abstract class AbstractModifier implements ModifierInterface
{
    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /*
     * Meta children path
     */
    const META_CHILDREN_PATH = '/arguments/data/children';

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
    protected $arrayManager;

    /** @var FormElementMapper  */
    private $formElementMapper;

    /** @var EavAttributeFactory  */
    private $eavAttributeFactory;

    /** @var DataPersistorInterface */
    private $dataPersistor;

    /** @var ConfiguratorOptionAttributeInterface[] */
    private $attributes = [];

    /** @var ConfiguratorOptionAttributeInterface[] */
    private $commonAttributes = [];

    /** @var Registry  */
    private $registry;

    /** @var Config  */
    private $eavConfig;

    /** @var array */
    private $attributesToDisable;

    /** @var OptionType  */
    private $optionTypeSource;

    /**
     * Eav constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfiguratorOptionAttributeRepositoryInterface $attributeRepository
     * @param ArrayManager $arrayManager
     * @param FormElementMapper $formElementMapper
     * @param EavAttributeFactory $eavAttributeFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param Config $eavConfig
     * @param OptionType $optionTypeSource
     * @param array $attributesToDisable
     */
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
        $attributesToDisable = []
    ) {
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->attributeRepository      = $attributeRepository;
        $this->arrayManager             = $arrayManager;
        $this->formElementMapper        = $formElementMapper;
        $this->eavAttributeFactory      = $eavAttributeFactory;
        $this->dataPersistor            = $dataPersistor;
        $this->registry                 = $registry;
        $this->eavConfig                = $eavConfig;
        $this->optionTypeSource         = $optionTypeSource;
        $this->attributesToDisable      = $attributesToDisable;
    }

    /**
     * @param null string $apply
     * @return ConfiguratorOptionAttributeInterface|ConfiguratorOptionAttributeInterface[]
     */
    public function getAttributes($apply = null)
    {
        if ($apply===null) {
            if (!$this->commonAttributes) {
                $this->searchCriteriaBuilder->addFilter('apply_to', $apply, 'null');
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $this->commonAttributes = $this->attributeRepository->getList($searchCriteria)->getItems();
            }
            return $this->commonAttributes;
        } else {
            if (!isset($this->attributes[$apply]) || !$this->attributes[$apply]) {
                $this->searchCriteriaBuilder->addFilter('apply_to', $apply);
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $this->attributes[$apply] = $this->attributeRepository->getList($searchCriteria)->getItems();
            }
            return $this->attributes[$apply];
        }
    }

    public function setupAttributeMeta($attribute, $sortOrder = 0)
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

        if ($attribute->getIsRequired()) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'validation' => ['required-entry' => true],
            ]);
        }
        $validations = explode(' ', $attribute->getFrontendClass());

        foreach ($validations as $validation) {
            if ($validation) {
                $meta = $this->arrayManager->merge($configPath . '/validation', $meta, [
                    $validation => true,
                ]);
            }
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
            case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            /*case 'price':
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

    /**
     * @param EavAttribute $attribute
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface|null
     */
    private function getAttributeModel($attribute)
    {
        try {
            $attributeModel = $this->attributeRepository->get($attribute->getAttributeCode());
        } catch (NoSuchEntityException $exception) {
            return null;
        }
        return $attributeModel;
    }

    /**
     * Customize checkboxes
     *
     * @param EavAttribute $attribute
     * @param array $meta
     * @return array
     */
    private function customizeCheckbox(EavAttribute $attribute, array $meta)
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

    /**
     * Add wysiwyg properties
     *
     * @param EavAttribute $attribute
     * @param array $meta
     * @return array
     */
    private function customizeWysiwyg(EavAttribute $attribute, array $meta)
    {
        if ($attribute->getAttributeCode() !== 'description') {
            return $meta;
        }

        $meta['arguments']['data']['config']['formElement'] = WysiwygElement::NAME;
        $meta['arguments']['data']['config']['wysiwyg'] = true;
        $meta['arguments']['data']['config']['wysiwygConfigData'] = [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];

        return $meta;
    }
}
