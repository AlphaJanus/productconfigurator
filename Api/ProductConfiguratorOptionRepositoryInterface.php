<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 05.07.18
 * Time: 17:55
 */

namespace Netzexpert\ProductConfigurator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;

/**
 * Interface ProductConfiguratorOptionRepositoryInterface
 * @package Netzexpert\ProductConfigurator\Api
 */
interface ProductConfiguratorOptionRepositoryInterface
{
    /**
     * @param ProductConfiguratorOptionInterface $productConfiguratorOption
     * @return ProductConfiguratorOptionInterface
     * @throws CouldNotSaveException
     */
    public function save(ProductConfiguratorOptionInterface $productConfiguratorOption);

    /**
     * @param int $id
     * @return ProductConfiguratorOptionInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\ProductConfiguratorOptionSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ProductConfiguratorOptionInterface $productConfiguratorOption
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ProductConfiguratorOptionInterface $productConfiguratorOption);

    /**
     * @param $id
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);
}
