<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 26.03.19
 * Time: 12:01
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class SelectionWidget extends Template
{

    public function isEnabled()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            return $this->_scopeConfig->getValue(
                'configurator/selection_widget/enabled',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }
}
