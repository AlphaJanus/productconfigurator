<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.05.18
 * Time: 14:12
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface ConfiguratorOptionVariantInterface extends ExtensibleDataInterface
{
    const ID            = 'value_id';
    const OPTION_ID     = 'option_id';
    const TITLE         = 'title';
    const VALUE         = 'value';
    const SORT_ORDER    = 'sort_order';
    const PRICE         = 'price';
    const IS_DEFAULT    = 'is_default';
    const IMAGE         = 'image';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string$value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * @return bool
     */
    public function getIsDefault();

    /**
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);

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
