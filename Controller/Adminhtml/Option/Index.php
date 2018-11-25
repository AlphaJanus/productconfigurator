<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 16:16
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Authorization level of a ProductConfigurator options
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /** @var PageFactory  */
    private $resultPageFactory;

    /**
     * Index constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory
    ) {
        $this->resultPageFactory    = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Netzexpert_ProductConfigurator::options');
        $resultPage->getConfig()->getTitle()->prepend(__('Configurator options'));
        return $resultPage;
    }
}
