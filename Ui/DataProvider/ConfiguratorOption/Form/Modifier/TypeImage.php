<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 15.04.19
 * Time: 13:15
 */

namespace Netzexpert\ProductConfigurator\Ui\DataProvider\ConfiguratorOption\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;

class TypeImage extends AbstractModifier
{
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

        $containerMeta[static::CONTAINER_PREFIX . 'for_image']['children'] = [];
        foreach ($this->getAttributes('image') as $attribute) {
            $containerMeta = $this->arrayManager
                ->set(
                    $configPath,
                    [],
                    [
                        'componentType'     => Container::NAME,
                        'formElement'       => Container::NAME,
                        'deps'              => [
                            "configurator_option_form.option_form_data_source",
                            "configurator_option_form.configurator_option_form"
                        ],
                        'breakLine'         => false,
                        'sortOrder'         => $sortOrder * self::SORT_ORDER_MULTIPLIER,
                        'visible'           => false
                    ]
                );
            $containerMeta['children'][$attribute->getAttributeCode()] =
                $this->setupAttributeMeta($attribute, $attribute->getSortOrder());
            $meta['general']['children'][static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $containerMeta;
        }
        return $meta;
    }

}
