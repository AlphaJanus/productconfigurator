<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 15:43
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionVariantInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant as VariantResource;

class ProductConfiguratorOptionVariant extends AbstractModel implements ProductConfiguratorOptionVariantInterface
{
    /** @var Json  */
    private $serializer;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        Json $serialiser,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serialiser;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(VariantResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::VARIANT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getConfiguratorOptionId()
    {
        return $this->getData(self::CONFIGURATOR_OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getValueId()
    {
        return $this->getData(self::VALUE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function getIsDependent()
    {
        return $this->getData(self::IS_DEPENDENT);
    }

    /**
     * @inheritDoc
     */
    public function getAllowedVariants()
    {
        return $this->getData(self::ALLOWED_VARIANTS);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return $this->serializer->unserialize($this->getData(self::DEPENDENCIES));
    }


    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::VARIANT_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * @inheritDoc
     */
    public function setConfiguratorOptionId($configuratorOptionId)
    {
        return $this->setData(self::CONFIGURATOR_OPTION_ID, $configuratorOptionId);
    }

    /**
     * @inheritDoc
     */
    public function setValueId($valueId)
    {
        return $this->setData(self::VALUE_ID, $valueId);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData(self::ENABLED, $isEnabled);
    }

    /**
     * @inheritDoc
     */
    public function setIsDependent($isDependent)
    {
        return $this->setData(self::IS_DEPENDENT, $isDependent);
    }

    /**
     * @inheritDoc
     */
    public function setAllowedVariants($variants)
    {
        return $this->setData(self::ALLOWED_VARIANTS, $variants);
    }

    /**
     * @inheritDoc
     */
    public function setDependencies($dependencies)
    {
        return $this->setData(self::DEPENDENCIES, $this->serializer->serialize($dependencies));
    }


}
