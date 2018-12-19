<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:52
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption as ConfiguratorOptionResource;

class ConfiguratorOption extends AbstractModel implements ConfiguratorOptionInterface
{

    const ENTITY = 'configurator_option_entity';

    protected $_eventPrefix = 'configurator_option_entity';

    /** @var ConfiguratorOptionResource\Variant\CollectionFactory  */
    private $variantCollectionFactory;

    /** @var ConfiguratorOptionResource\Variant\Collection | null */
    private $variants;

    /** @var ConfiguratorOptionVariantsProcessor  */
    private $optionVariantsProcessor;

    /**
     * ConfiguratorOption constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ConfiguratorOptionResource\Variant\CollectionFactory $variantCollectionFactory
     * @param ConfiguratorOptionVariantsProcessor $optionVariantsProcessor
     * @param ConfiguratorOptionResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ConfiguratorOptionResource\Variant\CollectionFactory $variantCollectionFactory,
        ConfiguratorOptionVariantsProcessor $optionVariantsProcessor,
        ResourceModel\ConfiguratorOption $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->variantCollectionFactory = $variantCollectionFactory;
        $this->optionVariantsProcessor  = $optionVariantsProcessor;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize configurator option model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ConfiguratorOptionResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {

        return ($this->getData(self::VALUES)) ? $this->getData(self::VALUES) : [];
    }

    /**
     * @inheritdoc
     */
    public function getVariants()
    {
        if (!$this->variants) {
            $this->variants = $this->variantCollectionFactory->create()
                ->joinProductVariantsData()
                ->addFieldToFilter('main_table.configurator_option_id', ['eq' => $this->getId()])
                ->setOrder('sort_order', Collection::SORT_ORDER_ASC);
        }
        return $this->variants;
    }

    /**
     * @inheritdoc
     */
    public function hasVariants()
    {
        return in_array($this->getType(), $this->getTypesWithVariants());
    }

    /**
     * @inheritdoc
     */
    public function setValues($values)
    {
        return $this->setData(self::VALUES, $values);
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        $this->optionVariantsProcessor->processVariants($this);
        return parent::afterSave();
    }

    /**
     * @return array
     */
    public function getTypesWithVariants()
    {
        return [
            OptionType::TYPE_SELECT,
            OptionType::TYPE_RADIO,
            OptionType::TYPE_IMAGE,
        ];
    }
}
