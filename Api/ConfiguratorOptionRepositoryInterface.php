<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 14:15
 */

namespace Netzexpert\ProductConfigurator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionSearchResultsInterface;

interface ConfiguratorOptionRepositoryInterface
{
    /**
     * @param int $id
     * @return ConfiguratorOptionInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param ConfiguratorOptionInterface $configuratorOption
     * @return ConfiguratorOptionInterface
     * @throws CouldNotSaveException
     */
    public function save(ConfiguratorOptionInterface $configuratorOption);

    /**
     * Retrieve vendors matching specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ConfiguratorOptionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ConfiguratorOptionInterface $configuratorOption
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ConfiguratorOptionInterface $configuratorOption);

    /**
     * @param int $id
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);
}
