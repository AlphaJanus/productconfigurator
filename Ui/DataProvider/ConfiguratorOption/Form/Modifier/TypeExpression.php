<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 29.05.18
 * Time: 13:06
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\View\LayoutFactory;
use Magento\Ui\Component\Container;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\ExpressionHint as ExpressionHintBlock;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

class TypeExpression extends AbstractModifier
{

    /** @var LayoutFactory  */
    private $layoutFactory;

    /** @var Registry  */
    private $registry;

    /**
     * TypeExpression constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfiguratorOptionAttributeRepositoryInterface $attributeRepository
     * @param ArrayManager $arrayManager
     * @param FormElementMapper $formElementMapper
     * @param EavAttributeFactory $eavAttributeFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param Config $eavConfig
     * @param OptionType $optionTypeSource
     * @param LayoutFactory $layoutFactory
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
        LayoutFactory $layoutFactory,
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
        $this->layoutFactory    = $layoutFactory;
        $this->registry         = $registry;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = count($this->arrayManager->get('general/children', $meta)) + 1;
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

        $containerMeta[static::CONTAINER_PREFIX . 'for_expression']['children'] = [];
        foreach ($this->getAttributes('expression') as $attribute) {
            $containerMeta = $this->arrayManager
                ->set(
                    $configPath,
                    [],
                    [
                        'componentType'     => Container::NAME,
                        'formElement'       => Container::NAME,
                        'component'         => 'Netzexpert_ProductConfigurator/js/form/components/type-expression',
                        'deps'              => [
                            "configurator_option_form.option_form_data_source",
                            "configurator_option_form.configurator_option_form"
                        ],
                        'breakLine'         => false,
                        'sortOrder'         => $sortOrder * self::SORT_ORDER_MULTIPLIER,
                        'visible'           => false
                    ]
                );
            $containerMeta['children'][$attribute->getAttributeCode()] =
                $this->setupAttributeMeta($attribute, $attribute->getSortOrder());
            if ($attribute->getAttributeCode() =="expression") {
                $containerMeta = $this->arrayManager->merge(
                    'children/' . $attribute->getAttributeCode() . '/' .$configPath,
                    $containerMeta,
                    [
                        'component' => 'Netzexpert_ProductConfigurator/js/form/element/expression',
                        'deps'      => [
                            "configurator_option_form.option_form_data_source",
                            "configurator_option_form.configurator_option_form",
                            'configurator_option_form.configurator_option_form.general.container_expression'
                        ]
                    ]
                );
            }
            $meta['general']['children'][static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $containerMeta;
        }

        $expressionPath = 'general/children/container_expression/children/expression';
        $meta = $this->arrayManager->merge(
            $expressionPath . '/arguments/data/config',
            $meta,
            [
                'notice' => $this->getExpressionNotice()
            ]
        );
        return $meta;
    }

    private function getExpressionNotice()
    {
        /** @var ExpressionHintBlock $notice */
        $notice = $this->layoutFactory->create()
            ->createBlock('Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit\ExpressionHint');
        $notice->setData('current_id', $this->registry->registry('configurator_option')->getId());
        $notice->setTemplate('option/edit/expression-hint.phtml');
        return $notice->toHtml();
    }
}
