<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 14:00
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;

class NewAction extends Action
{
    /**
     * Authorization level of a ProductConfigurator options
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /** @var ForwardFactory  */
    private $resultForwardFactory;

    /**
     * NewAction constructor.
     * @param Action\Context $context
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $forwardFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
