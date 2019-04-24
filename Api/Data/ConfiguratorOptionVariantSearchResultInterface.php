<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 04.05.18
 * Time: 10:35
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

interface ConfiguratorOptionVariantSearchResultInterface extends SearchResultInterface
{
    /**
     * @return ConfiguratorOptionVariantInterface[]
     */
    public function getItems();

    /**
     * @param ConfiguratorOptionVariantInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
