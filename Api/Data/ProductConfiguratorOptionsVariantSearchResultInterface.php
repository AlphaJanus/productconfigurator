<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.04.19
 * Time: 11:25
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

interface ProductConfiguratorOptionsVariantSearchResultInterface extends SearchResultInterface
{
    /**
     * @return ProductConfiguratorOptionVariantInterface[]
     */
    public function getItems();

    /**
     * @param ProductConfiguratorOptionVariantInterface[] $items
     * @return $this
     */
    public function setItems($items);

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
