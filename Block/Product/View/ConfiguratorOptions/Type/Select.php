<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:22
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class Select extends AbstractOptions
{
    public function getValuesHtml()
    {
        $option = $this->getOption();
        $parentOptionDefaultValue = $this->getParentOptionDefaultValue();
        $extraParams = '';
        $require = $option->getIsRequired() ? ' required' : '';
        $params = [];

        $value = $this->getDefaultValue();

        $select = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setData(
            [
                'id' => 'select_' . $option->getData('code'),
                'class' => $require . ' product-configurator-option admin__control-select'
            ]
        );
        $select->setName('configurator_options[' . $option->getId(). ']')->addOption('', __('-- Please Select --'));
        foreach ($this->getValuesData() as $_value) {
            if ($_value['is_dependent'] && !in_array($parentOptionDefaultValue, $_value['allowed_variants'])) {
                $params = ['disabled' => true];
            }
            if ($_value['enabled']) {
                $select->addOption(
                    $_value['value_id'],
                    $_value['title'],
                    $params
                );
            }
        }

        $extraParams .= ' data-selector="' . $select->getName() . '" data-code="' . $option->getData('code') . '"';
        $select->setExtraParams($extraParams);

        if ($value != null) {
            $select->setValue($value);
        }

        return $select->getHtml();
    }

    public function getDefaultValue()
    {
        $configuredValue = $this->getProduct()
            ->getPreconfiguredValues()
            ->getData('configurator_options/' . $this->getOption()->getId());
        if ($configuredValue) {
            return $configuredValue;
        }
        $default = null;
        $fistActive = null;
        foreach ($this->getValuesData() as $value) {
            if ($value['enabled'] && !$fistActive) {
                $fistActive = $value['value_id'];
            }
            if ($value['is_default'] && $value['enabled']) {
                $default = $value['value_id'];
            }
        }
        return ($default) ? $default : $fistActive;
    }
}
