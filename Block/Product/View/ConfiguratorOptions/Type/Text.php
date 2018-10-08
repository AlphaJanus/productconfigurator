<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 04.09.18
 * Time: 16:39
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class Text extends AbstractOptions
{

    public function getDefaultValue()
    {
        $configuredValue = $this->getProduct()
            ->getPreconfiguredValues()
            ->getData('configurator_options/' . $this->getOption()->getId());
        return $configuredValue ? $configuredValue : $this->getOption()->getData('default_value');
    }
}
