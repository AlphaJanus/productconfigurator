<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:23
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ConfiguratorOptionAttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
    const ENTITY_TYPE_CODE = 'configurator_option_entity';

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);
}
