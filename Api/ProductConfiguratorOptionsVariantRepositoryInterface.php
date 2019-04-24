<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.04.19
 * Time: 11:17
 */

namespace Netzexpert\ProductConfigurator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionsVariantSearchResultInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionVariantInterface;

interface ProductConfiguratorOptionsVariantRepositoryInterface
{
    /**
     * @param ProductConfiguratorOptionVariantInterface $variant
     * @return ProductConfiguratorOptionVariantInterface
     * @throws CouldNotSaveException
     */
    public function save(ProductConfiguratorOptionVariantInterface $variant);

    /**
     * @param int $id
     * @return ProductConfiguratorOptionVariantInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param ProductConfiguratorOptionVariantInterface $variant
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ProductConfiguratorOptionVariantInterface $variant);

    /**
     * @param int $id
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductConfiguratorOptionsVariantSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
