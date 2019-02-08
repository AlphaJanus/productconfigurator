<?php
/**
 * Created by andrew.
 * Date: 26.11.18
 * Time: 15:08
 */

namespace Netzexpert\ProductConfigurator\Api\Data;

interface ProductConfiguratorOptionVariantInterface
{
    const VARIANT_ID                = 'variant_id';
    const VALUE_ID                  = 'value_id';
    const OPTION_ID                 = 'option_id';
    const CONFIGURATOR_OPTION_ID    = 'configurator_option_id';
    const PRODUCT_ID                = 'product_id';
    const ENABLED                   = 'enabled';
    const IS_DEPENDENT              = 'is_dependent';
    const ALLOWED_VARIANTS          = 'allowed_variants';
    const DEPENDENCIES              = 'dependencies';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @return int
     */
    public function getConfiguratorOptionId();

    /**
     * @return int
     */
    public function getValueId();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getIsEnabled();

    /**
     * @return int
     */
    public function getIsDependent();

    /**
     * @return array
     */
    public function getAllowedVariants();

    /**
     * @return array
     */
    public function getDependencies();

    /**
     * @param $id int
     * @return $this
     */
    public function setId($id);

    /**
     * @param $optionId int
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * @param $configuratorOptionId int
     * @return $this
     */
    public function setConfiguratorOptionId($configuratorOptionId);

    /**
     * @param $valueId int
     * @return $this
     */
    public function setValueId($valueId);

    /**
     * @param $productId int
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @param $isEnabled int
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * @param $isDependent int
     * @return $this
     */
    public function setIsDependent($isDependent);

    /**
     * @param $variants array
     * @return $this
     */
    public function setAllowedVariants($variants);

    /**
     * @param $dependencies array
     * @return $this
     */
    public function setDependencies($dependencies);
}
