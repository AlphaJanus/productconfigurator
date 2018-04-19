<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 14:11
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterfaceFactory;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ConfiguratorOptionInterfaceFactory  */
    private $configuratorOptionFactory;

    /** @var PageFactory  */
    private $resultPageFactory;

    /** @var Registry  */
    private $registry;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ConfiguratorOptionInterfaceFactory $configuratorOptionInterfaceFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ConfiguratorOptionInterfaceFactory $configuratorOptionInterfaceFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory            = $pageFactory;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->configuratorOptionFactory    = $configuratorOptionInterfaceFactory;
        $this->registry                     = $registry;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Netzexpert_ProductConfigurator::options')
            ->addBreadcrumb(__('Configurator options'), __('Configurator options'))
            ->addBreadcrumb(__('Manage options'), __('Manage options'));
        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->_request->getParam('id');

        if ($id) {
            try {
                $configuratorOption = $this->configuratorOptionRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This option no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $configuratorOption = $this->configuratorOptionFactory->create();
        }
        $this->registry->register('configurator_option', $configuratorOption);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit option') : __('New option'),
            $id ? __('Edit option') : __('New option')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Configurator options'));
        $resultPage->getConfig()->getTitle()->prepend(
            $configuratorOption->getId() ? $configuratorOption->getName() : __('New option')
        );
        return $resultPage;
    }
}
