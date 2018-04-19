<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:23
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ConfiguratorOptionAttributeInterface extends \Magento\Catalog\Api\Data\EavAttributeInterface
{
    const ENTITY_TYPE_CODE = 'configurator_option_entity';
}
