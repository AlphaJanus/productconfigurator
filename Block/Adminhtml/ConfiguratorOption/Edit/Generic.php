<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 17.04.18
 * Time: 19:35
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Generic implements ButtonProviderInterface
{
    /** @var Context */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }
}
