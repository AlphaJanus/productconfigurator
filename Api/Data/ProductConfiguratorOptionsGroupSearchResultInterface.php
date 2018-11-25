<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 16:12
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

interface ProductConfiguratorOptionsGroupSearchResultInterface extends SearchResultInterface
{
    /**
     * @return ProductConfiguratorOptionsGroupInterface[]
     */
    public function getItems();

    /**
     * @param ProductConfiguratorOptionsGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);
}
