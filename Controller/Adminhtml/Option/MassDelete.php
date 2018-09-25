<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 20.09.18
 * Time: 16:50
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;

class MassDelete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $optionRepository;

    /**
     * MassDelete constructor.
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


    public function execute()
    {
        $ids = $this->getRequest()->getParam('selected', []);

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $i = 0;
        foreach ($ids as $id) {
            try {
                $this->optionRepository->deleteById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('We can\'t find option to delete'));
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotDeleteException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
            $i++;
        }
        $this->messageManager->addSuccess(__('A total of %1 option(s) have been deleted.', $i));

        return $resultRedirect->setPath('*/*/');
    }
}
