<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 14:32
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

/**
 * Interface ProductConfiguratorOptionInterface
 * @package Netzexpert\ProductConfigurator\Api\Data
 */
interface ProductConfiguratorOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID                        = 'option_id';
    const PRODUCT_ID                = 'product_id';
    const CONFIGURATOR_OPTION_ID    = 'configurator_option_id';
    const GROUP_ID                  = 'group_id';
    const POSITION                  = 'position';
    const PARENT_OPTION             = 'parent_option';
    const VALUES_DATA               = 'values_data';

    /**
     * @return int | null
     */
    public function getId();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getConfiguratorOptionId();

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return int
     */
    public function getParentOption();

    /**
     * @return array
     */
    public function getValuesData();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @param int $configuratorOptionId
     * @return $this
     */
    public function setConfiguratorOptionId($configuratorOptionId);

    /**
     * @param int $groupId
     * @return $this
     */
    public function setGroupId($groupId);

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * @param int $parentOption
     * @return $this
     */
    public function setParentOption($parentOption);

    /**
     * @param $valuesData
     * @return $this
     */
    public function setValuesData($valuesData);

    /**
     * @param string|array $additionalData
     * @param mixed $value
     * @return $this
     */
    public function setAdditionalData($additionalData, $value = null);

    /**
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null);
}
