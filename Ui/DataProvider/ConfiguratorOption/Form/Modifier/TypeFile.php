<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 15.01.19
 * Time: 15:36
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;

class TypeFile extends AbstractModifier
{

    const TYPE = 'file';
    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = count($this->arrayManager->get('general/children', $meta)) + 1;
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

        $containerMeta[static::CONTAINER_PREFIX . 'for_file']['children'] = [];
        $containerMeta = $this->arrayManager
            ->set(
                $configPath,
                [],
                [
                    'componentType'     => Container::NAME,
                    'formElement'       => Container::NAME,
                    'component'         => 'Netzexpert_ProductConfigurator/js/form/components/type-file',
                    'deps'              => [
                        "configurator_option_form.option_form_data_source",
                        "configurator_option_form.configurator_option_form"
                    ],
                    'breakLine'         => false,
                    'sortOrder'         => $sortOrder * self::SORT_ORDER_MULTIPLIER,
                    'visible'           => false
                ]
            );
        foreach ($this->getAttributes('file') as $attribute) {
            $containerMeta['children'][$attribute->getAttributeCode()] =
                $this->setupAttributeMeta($attribute, $attribute->getSortOrder());
            if ($attribute->getAttributeCode() =="extensions") {
                $containerMeta = $this->arrayManager->merge(
                    'children/' . $attribute->getAttributeCode() . '/' .$configPath,
                    $containerMeta,
                    [
                        'component' => 'Netzexpert_ProductConfigurator/js/form/element/extensions',
                        'deps'      => [
                            "configurator_option_form.option_form_data_source",
                            "configurator_option_form.configurator_option_form"
                        ]
                    ]
                );
            }
        }
        $meta = $this->arrayManager->set(
            'general/children/' . static::CONTAINER_PREFIX . 'for_file',
            $meta,
            $containerMeta
        );

        return $meta;
    }
}
