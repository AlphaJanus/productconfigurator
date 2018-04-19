<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 17.04.18
 * Time: 19:38
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

class Back extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
