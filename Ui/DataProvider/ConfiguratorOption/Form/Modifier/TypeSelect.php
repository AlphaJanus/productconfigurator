<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 26.04.18
 * Time: 12:06
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Boolean;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\CollectionFactory;

class TypeSelect extends AbstractModifier
{

    const FIELD_IS_DELETE       = 'is_delete';

    const GROUP_VALUES_NAME     = 'values';

    const FIELD_ID_NAME         = 'value_id';
    const FIELD_TITLE_NAME      = 'title';
    const FIELD_VALUE_NAME      = 'value';
    const FIELD_PRICE_NAME      = 'price';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_IS_DEFAULT_NAME = 'is_default';

    /** @var ArrayManager  */
    private $arrayManager;

    /** @var StoreManagerInterface  */
    private $storeManager;

    /** @var Registry  */
    private $registry;

    /**
     * TypeSelect constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfiguratorOptionAttributeRepositoryInterface $attributeRepository
     * @param ArrayManager $arrayManager
     * @param FormElementMapper $formElementMapper
     * @param EavAttributeFactory $eavAttributeFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param Config $eavConfig
     * @param OptionType $optionTypeSource
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $variantsCollectionFactory
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
        StoreManagerInterface $storeManager,
        CollectionFactory $variantsCollectionFactory,
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
        $this->arrayManager                 = $arrayManager;
        $this->storeManager                 = $storeManager;
        $this->registry                     = $registry;
        $this->variantsCollectionFactory    = $variantsCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $option = $this->registry->registry('configurator_option');
        if ($optionId = $option->getId()) {
            /** @var Collection $variants */
            $variants = $this->variantsCollectionFactory->create()
                ->addFieldToFilter('option_id', ['eq' => $optionId])->toArray();
            $data[$optionId]['option']['values'] = $variants['items'];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = count($this->arrayManager->get('general/children', $meta)) + 1;

        $container[static::CONTAINER_PREFIX . static::GROUP_VALUES_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'component'         => 'Netzexpert_ProductConfigurator/js/form/components/type-select',
                        'collapsible' => true,
                        'label' => __('Option Values'),
                        'sortOrder' => $sortOrder * static::SORT_ORDER_MULTIPLIER,
                        'opened' => true,
                    ],
                ],
            ],
            'children' => [
                static::GROUP_VALUES_NAME => $this->getSelectTypeGridConfig($sortOrder)
            ]
        ];
        $meta = $this->arrayManager->merge(
            'general/children',
            $meta,
            $container
        );
        return $meta;
    }

    private function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel'            => __('Add Value'),
                        'componentType'             => DynamicRows::NAME,
                        'component'                 => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses'         => 'admin__field-wide',
                        'deleteProperty'            => static::FIELD_IS_DELETE,
                        'deleteValue'               => '1',
                        'defaultRecord'             => true,
                        'sortOrder'                 => $sortOrder,
                        'identificationProperty'    => 'value_id'

                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'     => Container::NAME,
                                'component'         => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider'  => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate'        => true,
                                'is_collection'     => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_ID_NAME => $this->getVariantsFieldConfig(
                            0,
                            [
                                'label'     => __('ID'),
                                'dataScope' => static::FIELD_ID_NAME,
                                'visible'   => false,
                            ]
                        ),
                        static::FIELD_TITLE_NAME => $this->getVariantsFieldConfig(
                            10,
                            [
                                'label'                 => __('Title'),
                                'dataScope'             => static::FIELD_TITLE_NAME,
                                'validation'            => [
                                    'required-entry'    => true
                                ],
                            ]
                        ),
                        static::FIELD_VALUE_NAME => $this->getVariantsFieldConfig(
                            20,
                            [
                                'label'                 => __('Value'),
                                'dataScope'             => static::FIELD_VALUE_NAME,
                                'validation'            => [
                                    'required-entry'    => true
                                ],
                            ]
                        ),
                        static::FIELD_PRICE_NAME => $this->getVariantsFieldConfig(
                            30,
                            [
                                'label'         => __('Price'),
                                'dataScope'     => static::FIELD_PRICE_NAME,
                                'template'      => 'Magento_Catalog/form/field',
                                'component'   => 'Netzexpert_ProductConfigurator/js/components/option-select-component',
                                'dataType'      => Number::NAME,
                                'addbefore'     => $this->getCurrencySymbol(),
                                'validation'    => [
                                    'validate-zero-or-greater' => true
                                ],
                            ]
                        ),
                        static::FIELD_IS_DEFAULT_NAME => $this->getVariantsFieldConfig(
                            40,
                            [
                                'label'             => __('Is Default'),
                                'dataScope'         => static::FIELD_IS_DEFAULT_NAME,
                                'dataType'          => Boolean::NAME,
                                'formElement'       => Checkbox::NAME,
                                'component'         => 'Netzexpert_ProductConfigurator/js/components/values-checkbox',
                                'parentContainer'   => 'container_values',
                                'parentSelections'  => 'record',
                                'prefer'            => 'radio',
                                'value'             => '0',
                                'valueMap'          => ['false' => '0', 'true' => '1'],
                                'fit'               => true,
                            ]
                        ),
                        static::FIELD_SORT_ORDER_NAME => $this->getVariantsFieldConfig(
                            50,
                            [
                                'label'     => __('Sort order'),
                                'dataScope' => static::FIELD_SORT_ORDER_NAME,
                                'visible'   => false,
                            ]
                        ),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(60)
                    ]
                ]
            ]
        ];
    }

    private function getVariantsFieldConfig($sortOrder, array $options = [])
    {
        $config = array_replace_recursive(
            [
                'label'         => __('Name'),
                'componentType' => Field::NAME,
                'formElement'   => Input::NAME,
                'dataScope'     => static::FIELD_VALUE_NAME,
                'dataType'      => Text::NAME,
                'sortOrder'     => $sortOrder,
                'required'      => false
            ],
            $options
        );
        return  [
            'arguments' => [
                'data' => [
                    'config' => $config
                ],
            ],
        ];
    }

    /**
     * Get currency symbol
     *
     * @return string
     * @since 101.0.0
     */
    private function getCurrencySymbol()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }

    /**
     * Get config for hidden field used for removing rows
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    private function getIsDeleteFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'fit'           => true,
                        'sortOrder'     => $sortOrder
                    ],
                ],
            ],
        ];
    }
}