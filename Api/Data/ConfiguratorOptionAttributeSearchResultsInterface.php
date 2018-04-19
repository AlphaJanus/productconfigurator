<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:21
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ConfiguratorOptionAttributeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
