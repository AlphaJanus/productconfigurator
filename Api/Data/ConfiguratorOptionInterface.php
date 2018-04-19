<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 14:39
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ConfiguratorOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const NAME =  'name';

    const TYPE  = 'type';


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
    public function getType();

    /**
     * @param string $type
     * @return ConfiguratorOptionInterface
     */
    public function setType($type);

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
}
