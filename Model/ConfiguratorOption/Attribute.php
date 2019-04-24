<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 23.04.18
 * Time: 13:51
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface;

class Attribute extends \Magento\Eav\Model\Attribute implements ConfiguratorOptionAttributeInterface
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'configurator_option_eav_attribute';

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Eav\Model\ResourceModel\Entity\Attribute::class
        );
    }
}
