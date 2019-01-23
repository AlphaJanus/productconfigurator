<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 17.01.19
 * Time: 16:50
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class File extends AbstractOptions
{
    /**
     * Returns info of file
     *
     * @return \Magento\Framework\DataObject
     */
    public function getFileInfo()
    {
        $info = $this->getProduct()->getPreconfiguredValues()->getData('configurator_options/' . $this->getOption()->getId());
        if (empty($info)) {
            $info = new \Magento\Framework\DataObject();
        } elseif (is_array($info)) {
            $info = new \Magento\Framework\DataObject($info);
        }
        return $info;
    }
}
