<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 14:30
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

interface ConfiguratorOptionSearchResultsInterface extends SearchResultInterface
{
    /**
     * Get attributes list.
     *
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface[] $items
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
