<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 23.01.19
 * Time: 9:26
 */

namespace Netzexpert\ProductConfigurator\Controller\Download;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Download;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\Type\Configurator;

class DownloadConfiguratorOption extends Action
{
    /** @var ForwardFactory */
    private $forwardFactory;

    /** @var ConfiguratorOptionRepositoryInterface */
    private $optionRepository;

    /** @var Json  */
    private $serializer;

    private $download;

    /**
     * DownloadConfiguratorOption constructor.
     * @param Context $context
     * @param ForwardFactory $forwardFactory
     * @param ConfiguratorOptionRepositoryInterface $optionRepository
     * @param Json $serializer
     * @param Download $download
     */
    public function __construct(
        Context $context,
        ForwardFactory $forwardFactory,
        ConfiguratorOptionRepositoryInterface $optionRepository,
        Json $serializer,
        Download $download
    ) {
        $this->forwardFactory   = $forwardFactory;
        $this->optionRepository = $optionRepository;
        $this->serializer       = $serializer;
        $this->download         = $download;
        parent::__construct($context);
    }

    /**
     * Configurator options download action
     *
     * @return \Magento\Framework\Controller\Result\Forward | void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var $option \Magento\Quote\Model\Quote\Item\Option */
        $option = $this->_objectManager->create(
            \Magento\Quote\Model\Quote\Item\Option::class
        )->load($quoteItemOptionId);
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->forwardFactory->create();

        if (!$option->getId()) {
            return $resultForward->forward('noroute');
        }
        $optionId = null;
        if (strpos($option->getCode(), Configurator::CONFIGURATOR_OPTION_PREFIX) === 0) {
            $optionId = str_replace(Configurator::CONFIGURATOR_OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                $optionId = null;
            }
        }
        $configuratorOption = null;
        if ($optionId) {
            try {
                $configuratorOption = $this->optionRepository->get($optionId);
                if (!$configuratorOption || !$configuratorOption->getId() || $configuratorOption->getType() != 'file') {
                    return $resultForward->forward('noroute');
                }
                $info = $this->serializer->unserialize($option->getValue());
                if ($this->getRequest()->getParam('key') != $info['secret_key']) {
                    return $resultForward->forward('noroute');
                }
                $this->download->downloadFile($info);
            } catch (NoSuchEntityException $exception) {
                return $resultForward->forward('noroute');
            } catch (\Exception $e) {
                return $resultForward->forward('noroute');
            }
        }
        $this->endExecute();
    }

    /**
     * Ends execution process
     *
     * @return void
     */
    protected function endExecute()
    {
        return;
    }

}
