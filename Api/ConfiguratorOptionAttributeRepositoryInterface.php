<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:20
 */

namespace Netzexpert\ProductConfigurator\Api;

interface ConfiguratorOptionAttributeRepositoryInterface extends \Magento\Framework\Api\MetadataServiceInterface
{

    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode);

    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
