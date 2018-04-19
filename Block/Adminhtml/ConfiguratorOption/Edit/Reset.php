<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 19.04.18
 * Time: 14:25
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

class Reset extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
