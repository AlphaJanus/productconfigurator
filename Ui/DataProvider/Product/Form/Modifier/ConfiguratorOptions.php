<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 12.06.18
 * Time: 16:52
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;
use Psr\Log\LoggerInterface;

class ConfiguratorOptions extends AbstractModifier
{

    const GROUP_CONFIGURATOR_OPTIONS_NAME                   = 'configurator_options_group';
    const GROUP_CONFIGURATOR_OPTIONS_SCOPE                  = 'data.product';
    const GROUP_CONFIGURATOR_OPTIONS_DEFAULT_SORT_ORDER     = '100';

    const CONTAINER_HEADER_NAME                             = 'configurator_container_header';
    const CONTAINER_OPTION                                  = 'configurator_container_option';
    const GRID_OPTIONS_GROUP_NAME                           = 'configurator_option_groups';
    const GRID_OPTIONS_NAME                                 = 'assigned_configurator_options';

    const BUTTON_ADD                                        = 'button_add';
    const ADD_OPTION_MODAL                                  = 'assign_configurator_option_modal';
    const INSERT_LISTING_NAME                               = 'assign_configurator_option_grid';
    const CONFIGURATOR_OPTIONS_LISTING                      = 'product_configurator_options_listing';

    const FIELD_IS_DELETE                                   = 'is_delete';
    const FIELD_SORT_ORDER_NAME                             = 'position';
    const FIELD_OPTION_ID                                   = 'option_id';
    const FIELD_NAME                                        = 'name';
    const FIELD_CODE                                        = 'code';
    const FIELD_PARENT_OPTION                               = 'parent_option';
    const DELETE_BUTTON                                     = 'delete_button';

    const DEPENDENCY_CONTAINER                              = 'dependency_container';
    const DEPENDENCY                                        = 'dependency';
    const DEPENDENCY_GRID_NAME                              = 'dependency_grid';
    const ALLOWED_VARIANTS                                  = 'allowed_variants';

    /** @var LocatorInterface  */
    private $locator;

    /** @var UrlInterface  */
    private $urlBuilder;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var Json  */
    private $json;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ConfiguratorOptions constructor.
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->locator                      = $locator;
        $this->urlBuilder                   = $urlBuilder;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->json                         = $json;
        $this->logger                       = $logger;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        if ($product->getTypeId() == Configurator::TYPE_ID) {
            $groups = [];
            $productExtensions = $product->getExtensionAttributes();
            $configuratorOptionsGroups = $productExtensions->getConfiguratorOptions();
            $assignedOptions = [];
            $optionsGroups = $productExtensions->getConfiguratorOptionsGroups();
            if (!empty($optionsGroups)) {
                foreach ($optionsGroups as $optionsGroup) {
                    $group = $optionsGroup->getData();
                    $options = [];
                    $groupOptions = $configuratorOptionsGroups[$optionsGroup->getId()]['options'];
                    if (!empty($groupOptions)) {
                        foreach ($groupOptions as $option) {
                            try {
                                $configuratorOption = $this->configuratorOptionRepository
                                    ->get($option->getConfiguratorOptionId());
                            } catch (NoSuchEntityException $exception) {
                                $this->logger->error($exception->getMessage());
                            }
                            $values = $configuratorOption->getValues();
                            $valuesData = [];
                            $vData = $option->getValuesData();
                            if ($vData) {
                                $valuesData = $this->json->unserialize($vData);
                            }
                            foreach ($valuesData as &$val) {
                                unset($val['initialize']);
                            }
                            $valuesData = array_replace_recursive($values, $valuesData);
                            $configuratorOption->setValues($valuesData);
                            $options[] = array_merge(
                                $option->getData(),
                                $configuratorOption->getData()
                            );
                        }
                    }
                    $group['assigned_configurator_options'] = $options;
                    $assignedOptions[] = $options;
                    $groups[] = $group;
                }
            }

            return array_replace_recursive(
                $data,
                [
                    $this->locator->getProduct()->getId() => [
                        'product' => [
                            static::GRID_OPTIONS_GROUP_NAME => $groups,
                            static::INSERT_LISTING_NAME => $assignedOptions,
                            static::GRID_OPTIONS_NAME => $assignedOptions,
                        ]
                    ]
                ]
            );
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() == 'configurator') {
            $meta = array_replace_recursive(
                $meta,
                [
                    static::GROUP_CONFIGURATOR_OPTIONS_NAME => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Configurator Options'),
                                    'componentType' => Fieldset::NAME,
                                    'dataScope' => static::GROUP_CONFIGURATOR_OPTIONS_SCOPE,
                                    'collapsible' => true,
                                    'sortOrder' => static::GROUP_CONFIGURATOR_OPTIONS_DEFAULT_SORT_ORDER,
                                ],
                            ],
                        ],
                        'children' => [
                            static::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),
                            static::GRID_OPTIONS_GROUP_NAME => $this->getOptionGroupsConfig(20)
                        ]
                    ]
                ]
            );

            $meta = array_merge_recursive(
                $meta,
                [
                    static::ADD_OPTION_MODAL => $this->getAddOptionModalConfig()
                ]
            );
        }
        return $meta;
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    private function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Assigned Configurator Options'),
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __(
                            'Configurator options let customers choose the product configuration they want.'
                        ),
                    ],
                ],
            ],
            /*'children' => [
                static::BUTTON_ADD => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add Option'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/button',
                                'sortOrder' => 10,
                                'actions' => [
                                    [
                                        'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                            . static::ADD_OPTION_MODAL,
                                        'actionName' => 'openModal',
                                    ],
                                ],
                            ]
                        ],
                    ],
                ],
            ],*/
        ];
    }

    /**
     * Get config for the whole grid
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    private function getOptionsGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Option'),
                        'label' => __('Assigned Configurator Options'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Netzexpert_ProductConfigurator/js/dynamic-rows/dynamic-rows-grid',
                        'template' => 'Netzexpert_ProductConfigurator/dynamic-rows/templates/default',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteValue' => true,
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'addButton' => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader' => false,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                        'dataProvider' => 'data.product.' . static::INSERT_LISTING_NAME,
                        'identificationProperty' => 'entity_id',
                        'identificationDRProperty' => 'entity_id',
                        'map' => [
                            'entity_id' => 'entity_id',
                            'configurator_option_id' => 'entity_id',
                            'code' => 'code',
                            'name' => 'name',
                            'values' => 'values'
                        ],
                        'dndConfig' => [
                            'enabled' => true,
                            'component' => 'Netzexpert_ProductConfigurator/js/dynamic-rows/dnd'
                        ],
                        'links' => ['insertData' => '${ $.provider }:${ $.dataProvider }'],
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(40),
                        static::FIELD_OPTION_ID => $this->getOptionIdFieldConfig(10),
                        static::FIELD_NAME => $this->getNameTextConfig(20),
                        static::DELETE_BUTTON => $this->getDeleteButtonConfig(50),
                        static::FIELD_CODE => $this->getCodeFieldConfig(50),
                        static::DEPENDENCY_CONTAINER => $this->getDependencyBlock(60)
                    ]
                ]
            ]
        ];
    }

    private function getOptionGroupsConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel'            => __('Add Group'),
                        'componentType'             => DynamicRows::NAME,
                        'component'                 => 'Netzexpert_ProductConfigurator/js/dynamic-rows/dynamic-rows-groups',
                        'template'                  => 'Netzexpert_ProductConfigurator/dynamic-rows/templates/group',
                        'deleteProperty'            => static::FIELD_IS_DELETE,
                        'deleteValue'               => '1',
                        'defaultRecord'             => false,
                        'sortOrder'                 => $sortOrder,
                        'identificationProperty'    => 'group_id',
                        'additionalClasses'         => 'admin__field-wide',
                        'dndConfig' => [
                            'enabled' => true,
                        ]
                    ]
                ]
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'     => Container::NAME,
                                //'component'         => 'Magento_Ui/js/dynamic-rows/record',
                                'component'         => 'Netzexpert_ProductConfigurator/js/dynamic-rows/group-record',
                                'positionProvider'  => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate'        => true,
                                'is_collection'     => true,
                            ]
                        ]
                    ],
                    'children' => [
                        'group_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement'   => Input::NAME,
                                        'dataScope'     => 'group_id',
                                        'dataType'      => Text::NAME,
                                        'sortOrder'     => 10,
                                        'required'      => false,
                                        'visible'       => false
                                    ]
                                ]
                            ]
                        ],
                        'name' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType'     => Field::NAME,
                                        'formElement'       => Input::NAME,
                                        'dataScope'         => 'name',
                                        'dataType'          => Text::NAME,
                                        'sortOrder'         => 20,
                                        'additionalClasses' =>'group-name',
                                        'required'          => true
                                    ]
                                ]
                            ]
                        ],
                        'is_delete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType'     => ActionDelete::NAME,
                                        'fit'               => true,
                                        'additionalClasses' => 'group-delete',
                                        'sortOrder'         => 30
                                    ],
                                ],
                            ]
                        ],
                        'position' => $this->getPositionFieldConfig(40),
                        'options_container' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => '',
                                        'componentType' => Fieldset::NAME,
                                        'dataScope' => '',
                                        'additionalClasses' => 'admin__field-wide',
                                        'collapsible' => true,
                                        'opened' => true,
                                        'sortOrder' => 40,
                                    ]
                                ]
                            ],
                            'children' => [
                                'header' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'label' => '',
                                                'formElement' => Container::NAME,
                                                'componentType' => Container::NAME,
                                                'template' => 'ui/form/components/complex',
                                                'sortOrder' => $sortOrder,
                                            ],
                                        ],
                                    ],
                                    'children' => [
                                        static::BUTTON_ADD => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'title' => __('Add Option'),
                                                        'formElement' => Container::NAME,
                                                        'componentType' => Container::NAME,
                                                        'component' =>
                                                        'Netzexpert_ProductConfigurator/js/form/components/add-option',
                                                        'sortOrder' => 10,
                                                        'actions' => [
                                                            [
                                                                'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                                                    . static::ADD_OPTION_MODAL,
                                                                'actionName' => 'openModal',
                                                            ],
                                                        ],
                                                    ]
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                static::GRID_OPTIONS_NAME => $this->getOptionsGridConfig(30),
                                //static::ADD_OPTION_MODAL => $this->getAddOptionModalConfig()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for modal window "Add Option"
     *
     * @return array
     * @since 101.0.0
     */
    private function getAddOptionModalConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'component' => 'Magento_Ui/js/modal/modal-component',
                        'options' => [
                            'title' => __('Select Option'),
                            'buttons' => [
                                [
                                    'text' => __('Add selected options'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . static::INSERT_LISTING_NAME,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                static::INSERT_LISTING_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'cssclass' => 'noclass',
                                'componentType' => 'insertListing',
                                'component' => 'Netzexpert_ProductConfigurator/js/form/components/options-listing',
                                'dataScope' => 'data.product.' . static::INSERT_LISTING_NAME,
                                'externalProvider' => static::CONFIGURATOR_OPTIONS_LISTING . '.'
                                    . static::CONFIGURATOR_OPTIONS_LISTING . '_data_source',
                                'selectionsProvider' => static::CONFIGURATOR_OPTIONS_LISTING . '.'
                                    . static::CONFIGURATOR_OPTIONS_LISTING . '.option_columns.ids',
                                'ns' => static::CONFIGURATOR_OPTIONS_LISTING,
                                'externalFilterMode' => true,
                                'currentProductId' => $this->locator->getProduct()->getId(),
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'exports' => [
                                    'currentProductId' => '${ $.externalProvider }:params.current_product_id'
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for sorting
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    private function getPositionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_SORT_ORDER_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden id field
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    private function getOptionIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::FIELD_OPTION_ID,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                    ],
                ],
            ],
        ];
    }

    private function getNameTextConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'text',
                        'componentType' => 'text',
                        'template' => 'ui/form/element/text',
                        'additionalClasses' => 'admin__field-value',
                        'dataScope' => static::FIELD_NAME,
                        'inputName' => static::FIELD_NAME,
                        'sortOrder' => $sortOrder,
                        'visible' => true,
                    ]
                ]
            ]
        ];
    }

    private function getCodeFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'text',
                        'componentType' => 'text',
                        'template' => 'Netzexpert_ProductConfigurator/form/element/code',
                        'additionalClasses' => 'admin__field-value',
                        'dataScope' => static::FIELD_CODE,
                        'inputName' => static::FIELD_CODE,
                        'sortOrder' => $sortOrder,
                        'visible' => true,
                    ]
                ]
            ]
        ];
    }

    private function getDeleteButtonConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'formElement' => Input::NAME,
                        'fit'           => true,
                        'additionalClasses' => 'admin__field-complex-elements',
                        'sortOrder'     => $sortOrder
                    ],
                ],
            ],
        ];
    }

    private function getDependencyBlock($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Dependency Options'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'additionalClasses' => 'admin__field-wide',
                        'collapsible' => true,
                        'opened' => false,
                        'sortOrder' => $sortOrder,
                    ]
                ]
            ],
            'children' => [
                static::FIELD_PARENT_OPTION => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Parent Option'),
                                'componentType' => Field::NAME,
                                'formElement' => Select::NAME,
                                'dataType' => Select::NAME,
                                'default' => 0,
                                'component' => 'Netzexpert_ProductConfigurator/js/form/element/parent_option',
                                'dataScope' => static::FIELD_PARENT_OPTION,
                                'caption'   => __('No parent option'),
                                'sortOrder' => 0
                            ]
                        ]
                    ]
                ],
                'values' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'             => DynamicRows::NAME,
                                'component'
                                    => 'Netzexpert_ProductConfigurator/js/dynamic-rows/dependency-grid',
                                'additionalClasses'         => 'admin__field-wide',
                                'deleteProperty'            => static::FIELD_IS_DELETE,
                                'addButton'                 => false,
                                'deleteValue'               => '1',
                                'defaultRecord'             => false,
                                'dataScope'                 => '',
                                'dataProvider'              => '${ $.dataScope }.dependency',
                                'map'   => [
                                    'value_id' => 'value_id',
                                    'enabled' => 'enabled'
                                ],
                                'identificationProperty'    => 'value_id',
                                'identificationDRProperty'    => 'value_id',
                                'sortOrder'                 => 1
                            ]
                        ]
                    ],
                    'children' => [
                        'record' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Container::NAME,
                                        'isTemplate' => true,
                                        'is_collection' => true,
                                        'component' => 'Magento_Ui/js/dynamic-rows/record',
                                        'dataScope' => 'values',
                                    ],
                                ],
                            ],
                            'children' => [
                                'enabled' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'label' => __('Enabled'),
                                                'formElement' => Checkbox::NAME,
                                                'dataType' => Number::NAME,
                                                'componentType' => Field::NAME,
                                                'dataScope' => 'enabled',
                                                'sortOrder' => 0,
                                                'default' => 1,
                                                'initialValue' => 1,
                                                'checked' => 1,
                                                'prefer' => 'toggle',
                                                'valueMap' => [
                                                    'false' => '0',
                                                    'true' => '1'
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'title' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'text',
                                                'componentType' => 'text',
                                                'template' => 'ui/form/element/text',
                                                'inputName' => 'title',
                                                'dataType' => Text::NAME,
                                                'label' => __('Title'),
                                                'dataScope' => 'title',
                                                'sortOrder' => 1
                                            ],
                                        ],
                                    ],
                                ],
                                'is_dependent' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'label' => __('Is dependent'),
                                                'formElement' => Checkbox::NAME,
                                                'dataType' => Number::NAME,
                                                'componentType' => Field::NAME,
                                                'component' =>
                                                    'Netzexpert_ProductConfigurator/js/form/element/is_dependent',
                                                'dataScope' => 'is_dependent',
                                                'sortOrder' => 2,
                                                'default' => 0,
                                                'initialValue' => 0,
                                                'initialChecked' => false,
                                                'checked' => false,
                                                'prefer' => 'toggle',
                                                'valueMap' => [
                                                    'false' => '0',
                                                    'true' => '1'
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                static::ALLOWED_VARIANTS => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'label' => __('Allowed for parent values'),
                                                'componentType' => Field::NAME,
                                                'component' =>
                                                    'Netzexpert_ProductConfigurator/js/form/element/allowed_variants',
                                                'formElement' => MultiSelect::NAME,
                                                'dataType' => Select::NAME,
                                                'default' => [],
                                                'dataScope' => static::ALLOWED_VARIANTS,
                                                'sortOrder' => 3
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                        ],
                    ],
                ]
            ]
        ];
    }
}
