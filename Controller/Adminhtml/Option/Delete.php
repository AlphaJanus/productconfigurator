<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 19.04.18
 * Time: 15:05
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;

class Delete extends Action
{
    /** @var ConfiguratorOptionRepositoryInterface  */
    private $optionRepository;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param ConfiguratorOptionRepositoryInterface $optionRepository
     */
    public function __construct(
        Action\Context $context,
        ConfiguratorOptionRepositoryInterface $optionRepository
    ) {
        $this->optionRepository = $optionRepository;
        parent::__construct($context);
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->optionRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Option has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('We can\'t find option to delete'));
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotDeleteException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find option to delete'));
        return $resultRedirect->setPath('*/*/');
    }
}
