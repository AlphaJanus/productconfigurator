<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.05.18
 * Time: 16:34
 */

namespace Netzexpert\ProductConfigurator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface;

interface ConfiguratorOptionVariantRepositoryInterface
{
    /**
     * @param ConfiguratorOptionVariantInterface $variant
     * @return ConfiguratorOptionVariantInterface
     * @throws CouldNotSaveException
     */
    public function save(ConfiguratorOptionVariantInterface $variant);

    /**
     * @param int $id
     * @return ConfiguratorOptionVariantInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\ConfiguratorOptionVariantSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ConfiguratorOptionVariantInterface $variant
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ConfiguratorOptionVariantInterface $variant);

    /**
     * @param int $id
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);
}
