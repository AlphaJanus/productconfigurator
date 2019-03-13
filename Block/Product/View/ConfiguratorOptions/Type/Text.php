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

    public function getValidationRules()
    {
        $textValidate = null;
        $option = $this->getOption();
        if ($option->getIsRequired()) {
            $textValidate['required'] = true;
        }
        if ($min = $option->getData('min_value')) {
            $textValidate['gte'] = (float)$min;
        }
        if ($max = $option->getData('max_value')) {
            $textValidate['lte'] = (float)$max;
        }
        if ($option->getMaxCharacters()) {
            $textValidate['maxlength'] = $option->getMaxCharacters();
        }
        $rulesArray = explode(' ', $option->getData('validation'));
        $rules = [];
        foreach ($rulesArray as $class) {
            $rules = $this->mapRules($class, $rules);
        }
        $textValidate = array_merge($textValidate, $rules);
        return $this->json->serialize($textValidate);
    }

    /**
     * Map classes w. rules
     *
     * @param string $class
     * @param array $rules
     * @return array
     * @since 101.0.0
     */
    protected function mapRules($class, array $rules)
    {
        switch ($class) {
            case 'validate-number':
            case 'validate-digits':
            case 'validate-email':
            case 'validate-url':
            case 'validate-alpha':
            case 'validate-alphanum':
            case 'digits-two-after-comma':
                $rules = array_merge($rules, [$class => true]);
                break;
        }

        return $rules;
    }
}
