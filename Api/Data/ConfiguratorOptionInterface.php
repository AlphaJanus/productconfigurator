<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 14:39
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant\Collection;

interface ConfiguratorOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const NAME              = 'name';
    const CODE              = 'code';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';

    const TYPE              = 'type';
    const VALUES            = 'values';

    const IS_DUPLICATE      = 'is_duplicate';
    const ORIGINAL_LINK_ID  = "original_link_id";

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return ConfiguratorOptionInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return ConfiguratorOptionInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return ConfiguratorOptionInterface
     */
    public function setCode($code);

    /**
     * @return string | null
     */
    public function getCreatedAt();

    /**
     * @param $createdAt string | null
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string | null
     */
    public function getUpdatedAt();

    /**
     * @param $updatedAt string | null
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return ConfiguratorOptionInterface
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getValues();

    /**
     * @param array $values
     * @return $this
     */
    public function setValues($values);

    /**
     * @return Collection
     */
    public function getVariants();

    /**
     * @return bool
     */
    public function hasVariants();

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

    /**
     * Get object original data
     *
     * @param string $key
     * @return mixed
     */
    public function getOrigData($key = null);

    /**
     * @return array
     */
    public function getTypesWithVariants();

    /**
     * @return bool
     */
    public function isDuplicate();

    /**
     * @param $isDuplicate bool
     * @return $this
     */
    public function setIsDuplicate($isDuplicate);

    /**
     * @return int
     */
    public function getOriginalLinkId();

    /**
     * @param $id int
     * @return $this
     */
    public function setOriginalLinkId($id);
}
