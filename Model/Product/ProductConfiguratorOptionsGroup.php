<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 12:32
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Magento\Framework\Model\AbstractModel;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;

class ProductConfiguratorOptionsGroup extends AbstractModel implements ProductConfiguratorOptionsGroupInterface
{
    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
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
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }
}
