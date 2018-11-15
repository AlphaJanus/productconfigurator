<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 13:22
 */

namespace Netzexpert\ProductConfigurator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsGroupSearchResultInterface;

interface ProductConfiguratorOptionsGroupRepositoryInterface
{
    /**
     * @param ProductConfiguratorOptionsGroupInterface $optionsGroup
     * @return ProductConfiguratorOptionsGroupInterface
     * @throws CouldNotSaveException
     */
    public function save(ProductConfiguratorOptionsGroupInterface $optionsGroup);

    /**
     * @param int $id
     * @return ProductConfiguratorOptionsGroupInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductConfiguratorOptionsGroupSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ProductConfiguratorOptionsGroupInterface $optionsGroup
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ProductConfiguratorOptionsGroupInterface $optionsGroup);

    /**
     * @param int $id
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);
}
