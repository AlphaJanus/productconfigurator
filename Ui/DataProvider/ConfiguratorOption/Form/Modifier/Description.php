<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 05.12.18
 * Time: 11:45
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

class Description extends AbstractModifier
{

    /** @var ArrayManager  */
    private $arrayManager;

    /**
     * Description constructor.
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
        $descriptionPath = 'general/children/container_description/arguments/data/config';
        $configPath = ltrim($descriptionPath, ArrayManager::DEFAULT_PATH_DELIMITER);
        $meta = $this->arrayManager->merge(
            $configPath,
            $meta,
            [
                'component' => 'Magento_Ui/js/form/components/group',
            ]
        );
        return $meta;
    }
}
