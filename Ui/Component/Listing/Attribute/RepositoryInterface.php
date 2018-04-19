<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:39
 */

namespace Netzexpert\ProductConfigurator\Ui\Component\Listing\Attribute;

interface RepositoryInterface
{
    /**
     * Get attributes
     *
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface[]
     */
    public function getList();
}
