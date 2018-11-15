<?php
/**
 * Created by andrew.
 * Date: 12.11.18
 * Time: 12:20
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ProductConfiguratorOptionsGroupInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID            = 'group_id';
    const PRODUCT_ID    = 'product_id';
    const NAME          = 'name';
    const POSITION      = 'position';

    /**
     * @return int | null
     */
    public function getId();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string     $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key = '', $index = null);

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
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null);
}
