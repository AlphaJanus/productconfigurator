<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:14
 */

namespace Netzexpert\ProductConfigurator\Setup;


use Magento\Eav\Setup\EavSetup;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption;

class ConfiguratorOptionSetup extends EavSetup
{
    public function getDefaultEntities()
    {
        return [
            'configurator_option_entity' => [
                'entity_model'  => ConfiguratorOption::class,
                'table'         => 'configurator_option_entity',
                'attributes'    => [
                    'name'  => [
                        'type'  => 'static',
                        'label' => 'Name',
                        'input' => 'text'
                    ]
                ]
            ]
        ];
    }
}