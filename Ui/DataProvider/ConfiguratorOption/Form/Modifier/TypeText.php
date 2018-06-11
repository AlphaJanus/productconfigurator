<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 14.05.18
 * Time: 10:59
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

class TypeText extends AbstractModifier
{
    /** @var ArrayManager  */
    private $arrayManager;

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
        $this->arrayManager = $arrayManager;
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
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $containerMeta[static::CONTAINER_PREFIX . 'for_text'] = $this->arrayManager->set(
            $configPath,
            [],
            [
                'componentType'     => Container::NAME,
                'formElement'       => Container::NAME,
                'component'         => 'Netzexpert_ProductConfigurator/js/form/components/type-text',
                'breakLine'         => false,
                'showLabel'         => false,
                'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                'sortOrder'         => (count($meta['general']['children']) + 1) * self::SORT_ORDER_MULTIPLIER
            ]
        );
        $containerMeta[static::CONTAINER_PREFIX . 'for_text']['children'] = [];
        foreach ($this->getAttributes('text') as $attribute) {
            $containerMeta[static::CONTAINER_PREFIX . 'for_text']['children'][$attribute->getAttributeCode()] =
                $this->setupAttributeMeta($attribute, $attribute->getSortOrder());
        }
        $meta = $this->arrayManager->merge(
            'general/children',
            $meta,
            $containerMeta
        );
        return $meta;
    }
}
