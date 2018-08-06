<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 05.07.18
 * Time: 18:03
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Interface ProductConfiguratorOptionSearchResultInterface
 * @package Netzexpert\ProductConfigurator\Api\Data
 */
interface ProductConfiguratorOptionSearchResultInterface extends SearchResultInterface
{
    /**
     * @return ProductConfiguratorOptionInterface[]
     */
    public function getItems();

    /**
     * @param ProductConfiguratorOptionInterface[] $items
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
