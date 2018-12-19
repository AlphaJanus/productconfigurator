<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 8:29
 */

namespace Netzexpert\ProductConfigurator\Model\Quote\Item;

use Magento\Framework\Model\AbstractExtensibleModel;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorItemOptionValueInterface;

class ConfiguratorItemOptionValue extends AbstractExtensibleModel implements ConfiguratorItemOptionValueInterface
{
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
    public function setOptionId($id)
    {
        return $this->setData(self::OPTION_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getOptionTitle()
    {
        return $this->getData(self::OPTION_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionTitle($title)
    {
        return $this->setData(self::OPTION_TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValue($value)
    {
        return $this->setData(self::OPTION_VALUE, $value);
    }
}
