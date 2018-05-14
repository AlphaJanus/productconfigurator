<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.05.18
 * Time: 16:27
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Variant extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('configurator_option_entity_variants', 'value_id');
    }
}
