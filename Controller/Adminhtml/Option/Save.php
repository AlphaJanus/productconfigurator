<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 16.04.18
 * Time: 14:47
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterfaceFactory;

class Save extends Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $optionRepository;

    /** @var ConfiguratorOptionInterfaceFactory  */
    private $optionFactory;

    /** @var DataPersistorInterface  */
    private $dataPersistor;

    public function __construct(
        Action\Context $context,
        ConfiguratorOptionRepositoryInterface $optionRepository,
        ConfiguratorOptionInterfaceFactory $optionFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->optionRepository     = $optionRepository;
        $this->optionFactory        = $optionFactory;
        $this->dataPersistor        = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        if (!empty($data)) {
            if (isset($data['option']['entity_id'])) {
                try {
                    $option = $this->optionRepository->get($data['option']['entity_id']);
                } catch (NoSuchEntityException $exception) {
                    $this->messageManager->addErrorMessage(__('This option no longer exists'));
                }
            } else {
                $option = $this->optionFactory->create();
            }
            $option->setData($data['option']);

            try {
                $this->optionRepository->save($option);
                $this->messageManager->addSuccessMessage(__('Option was successfully saved'));
                $this->dataPersistor->clear('configurator_option');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $option->getId(), '_current' => true]);
                }
            } catch (CouldNotSaveException $exception) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving option: %1', $exception->getMessage())
                );
                $this->dataPersistor->set('configurator_option', $data);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
