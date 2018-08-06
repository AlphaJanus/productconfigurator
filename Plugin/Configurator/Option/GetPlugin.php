<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 17.07.18
 * Time: 14:24
 */

namespace Netzexpert\ProductConfigurator\Plugin\Configurator\Option;


use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface;

class GetPlugin
{
    public function afterGet(
        ConfiguratorOptionRepositoryInterface $optionRepository,
        ConfiguratorOptionInterface $option
    ) {
        if (in_array($option->getType(), $option->getTypesWithVariants())) {
            $values = [];
            /** @var ConfiguratorOptionVariantInterface $value */
            foreach ($option->getVariants()->getItems() as $value) {
                $values[] = $value->getData();
            }
            $option->setValues($values);
        }
        return $option;
    }
}
