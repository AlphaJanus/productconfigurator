<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:52
 */

namespace Netzexpert\ProductConfigurator\Model;

use Magento\Framework\Model\AbstractModel;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption as ConfiguratorOptionResource;

class ConfiguratorOption extends AbstractModel implements ConfiguratorOptionInterface
{

    const ENTITY = 'configurator_option_entity';
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
}
