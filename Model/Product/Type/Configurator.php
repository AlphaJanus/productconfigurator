<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 12:14
 */

namespace Netzexpert\ProductConfigurator\Model\Product\Type;


use Magento\Catalog\Model\Product\Type\AbstractType;

class Configurator extends AbstractType
{

    const TYPE_ID = 'configurator';
    /**
     * Delete data specific for Configurator product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {


    }

}