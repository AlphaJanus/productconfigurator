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
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ConfiguratorOptionResource::class);
    }
}