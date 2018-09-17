<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 04.09.18
 * Time: 16:39
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;

class Text extends AbstractOptions
{
    /** @var ProductConfiguratorOption */
    private $option;
}
