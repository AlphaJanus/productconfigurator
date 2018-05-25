<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:52
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\Manager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption as ConfiguratorOptionResource;
use Psr\Log\LoggerInterface;

class ConfiguratorOption extends AbstractModel implements ConfiguratorOptionInterface
{

    const ENTITY = 'configurator_option_entity';

    protected $_eventPrefix = 'configurator_option_entity';

    /** @var ConfiguratorOption\VariantRepository  */
    private $variantRepository;

    /** @var ConfiguratorOption\VariantFactory  */
    private $variantFactory;

    /** @var ConfiguratorOptionResource\Variant\CollectionFactory  */
    private $variantCollectionFactory;

    /** @var Manager  */
    private $messageManager;

    /** @var ConfiguratorOptionResource\Variant\Collection | null */
    private $variants;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * ConfiguratorOption constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ConfiguratorOption\VariantRepository $variantRepository
     * @param ConfiguratorOption\VariantFactory $variantFactory
     * @param ConfiguratorOptionResource\Variant\CollectionFactory $variantCollectionFactory
     * @param Manager $messageManager
     * @param LoggerInterface $logger
     * @param ConfiguratorOptionResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ConfiguratorOption\VariantRepository $variantRepository,
        ConfiguratorOption\VariantFactory $variantFactory,
        ConfiguratorOptionResource\Variant\CollectionFactory $variantCollectionFactory,
        Manager $messageManager,
        LoggerInterface $logger,
        ResourceModel\ConfiguratorOption $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->variantRepository        = $variantRepository;
        $this->variantFactory           = $variantFactory;
        $this->variantCollectionFactory = $variantCollectionFactory;
        $this->messageManager           = $messageManager;
        $this->logger                   = $logger;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize customer model
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

    public function getVariants()
    {
        if (!$this->variants) {
            $this->variants = $this->variantCollectionFactory->create()
                ->addFieldToFilter('option_id', ['eq' => $this->getId()]);
        }
        return $this->variants;
    }

    /**
     * @inheritdoc
     */
    public function setValues($values)
    {
        return $this->setData(self::VALUES, $values);
    }

    public function afterSave()
    {
        $this->processVariants();
        return parent::afterSave();
    }

    /**
     * @param ConfiguratorOption $option
     */
    private function processVariants()
    {
        if ($this->getType() == OptionType::TYPE_SELECT) {
            foreach ($this->getValues() as $value) {
                if ($value['value_id']) {
                    try {
                        $variant = $this->variantRepository->get($value['value_id']);
                    } catch (NoSuchEntityException $exception) {
                        $this->logger->error($exception->getMessage());
                    }
                } else {
                    unset($value['value_id']);
                    $variant = $this->variantFactory->create();
                }
                try {
                    $variant->setData($value);
                    if (isset($value['image'])) {
                        $variant->setImage($value["image"][0]["file"]);
                    } else {
                        $variant->setImage(null);
                    }
                    $variant->setOptionId($this->getId());
                    $this->variantRepository->save($variant);
                } catch (CouldNotSaveException $exception) {
                    $this->messageManager->addExceptionMessage($exception);
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }
}
