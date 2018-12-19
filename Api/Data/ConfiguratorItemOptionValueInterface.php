<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 8:16
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ConfiguratorItemOptionValueInterface
 * @package Netzexpert\ProductConfigurator\Api\Data
 */
interface ConfiguratorItemOptionValueInterface extends ExtensibleDataInterface
{
    /**
     * Configurator option id
     */
    const OPTION_ID = 'option_id';

    /**
     * Configurator option title
     */
    const OPTION_TITLE = 'option_title';

    /**
     * Configurator option value
     */
    const OPTION_VALUE = 'option_value';

    /**
     * @return string
     */
    public function getOptionId();

    /**
     * @param string $id
     * @return $this
     */
    public function setOptionId($id);

    /**
     * @return string
     */
    public function getOptionTitle();


    /**
     * @param string $title
     * @return $this
     */
    public function setOptionTitle($title);

    /**
     * @return string
     */
    public function getOptionValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setOptionValue($value);
}
