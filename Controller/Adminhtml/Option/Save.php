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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterfaceFactory;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Copier;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source\OptionType;

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

    /** @var ConfiguratorOptionVariantRepositoryInterface  */
    private $variantRepository;

    /** @var DataPersistorInterface  */
    private $dataPersistor;

    private $optionCopier;

    public function __construct(
        Action\Context $context,
        ConfiguratorOptionRepositoryInterface $optionRepository,
        ConfiguratorOptionInterfaceFactory $optionFactory,
        ConfiguratorOptionVariantRepositoryInterface $variantRepository,
        DataPersistorInterface $dataPersistor,
        Copier $optionCopier
    ) {
        $this->optionRepository     = $optionRepository;
        $this->optionFactory        = $optionFactory;
        $this->variantRepository    = $variantRepository;
        $this->dataPersistor        = $dataPersistor;
        $this->optionCopier         = $optionCopier;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back');
        $data = $this->getRequest()->getParams();
        $dataToUnset = [];
        if (!empty($data) && isset($data['option'])) {
            if (isset($data['option']['entity_id'])) {
                try {
                    $option = $this->optionRepository->get($data['option']['entity_id']);
                    $dataToUnset = array_diff_key($option->getOrigData(), $data['option']);
                } catch (NoSuchEntityException $exception) {
                    $this->messageManager->addErrorMessage(__('This option no longer exists'));
                }
            } else {
                $option = $this->optionFactory->create();
            }
            $this->deleteVariants($option, $data);
            $option->setData($data['option']);
            foreach (array_keys($dataToUnset) as $key) {
                $option->setData($key, null);
            }

            try {
                $this->optionRepository->save($option);
                $this->messageManager->addSuccessMessage(__('Option was successfully saved'));
                $this->dataPersistor->clear('configurator_option');
                if ($redirectBack === "duplicate") {
                    $newOption = $this->optionCopier->copy($option);
                }
            } catch (CouldNotSaveException $exception) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving option: %1', $exception->getMessage())
                );
                $this->dataPersistor->set('configurator_option', $data);
            }
        }
        if ($redirectBack === "duplicate" && !empty($newOption)) {
            $this->messageManager->addSuccessMessage(__('You duplicated the option.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $newOption->getId(), 'back' => null, '_current' => true]
            );
        }
        if ($redirectBack) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $option->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param ConfiguratorOptionInterface $option
     * @param array $data
     */
    private function deleteVariants($option, $data)
    {
        $variants = $option->getVariants()->toArray()['items'];
        foreach ($variants as $variant) {
            $exists = false;
            if (in_array($data['option']['type'], $this->getTypesWithVariants()) && isset($data['option']['values'])) {
                foreach ($data['option']['values'] as $value) {
                    if ($variant['value_id'] == $value['value_id']) {
                        $exists = true;
                    }
                }
            }
            if (!$exists) {
                try {
                    $this->variantRepository->deleteById($variant['value_id']);
                } catch (LocalizedException $exception) {
                    $this->messageManager->addExceptionMessage($exception);
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getTypesWithVariants()
    {
        return [
            OptionType::TYPE_SELECT,
            OptionType::TYPE_RADIO,
            OptionType::TYPE_IMAGE,
        ];
    }
}
