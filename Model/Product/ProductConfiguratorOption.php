<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 06.07.18
 * Time: 10:15
 */

namespace Netzexpert\ProductConfigurator\Model\Product;

use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;
use Magento\Framework\Model\AbstractModel;

class ProductConfiguratorOption extends AbstractModel implements ProductConfiguratorOptionInterface
{

    protected function _construct()
    {
        $this->_init('\Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption');
    }
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
    public function getConfiguratorOptionId()
    {
        return $this->getData(self::CONFIGURATOR_OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
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
    public function getParentOption()
    {
        return $this->getData(self::PARENT_OPTION);
    }

    /**
     * @inheritDoc
     */
    public function getValuesData()
    {
        return $this->getData(self::VALUES_DATA);
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
    public function setConfiguratorOptionId($configuratorOptionId)
    {
        return $this->setData(self::CONFIGURATOR_OPTION_ID, $configuratorOptionId);
    }

    /**
     * @inheritDoc
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @inheritDoc
     */
    public function setParentOption($parentOption)
    {
        return $this->setData(self::PARENT_OPTION, $parentOption);
    }

    /**
     * @inheritDoc
     */
    public function setValuesData($valuesData)
    {
        return $this->setData(self::VALUES_DATA, $valuesData);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData($additionalData, $value = null)
    {
        if ($additionalData === (array)$additionalData) {
            foreach ($additionalData as $key => $value) {
                $this->_data[$key] = $value;
            }
        } else {
            if (!array_key_exists($additionalData, $this->_data) || $this->_data[$additionalData] !== $value) {
                $this->_hasDataChanges = true;
            }
            $this->_data[$additionalData] = $value;
        }
        return $this;
    }
}
