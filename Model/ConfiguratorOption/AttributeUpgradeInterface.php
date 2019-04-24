<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.04.19
 * Time: 8:35
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

interface AttributeUpgradeInterface
{
    /**
     * @param array $attributesData
     * @return void
     */
    public function execute($attributesData);
}
