<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 19.04.18
 * Time: 14:45
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Context;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Psr\Log\LoggerInterface;

class Delete extends Generic
{
    /** @var Context */
    private $context;

    /** @var ConfiguratorOptionRepositoryInterface */
    private $optionRepository;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * Delete constructor.
     * @param Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        LoggerInterface $logger
    ) {
        $this->context          = $context;
        $this->optionRepository = $configuratorOptionRepository;
        $this->logger           = $logger;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getOptionId()) {
            $data = [
                'label' => __('Delete Option'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getOptionId()]);
    }

    /**
     * @return int|null
     */
    public function getOptionId()
    {
        $id = $this->context->getRequestParam('id');
        if ($id) {
            try {
                return $this->optionRepository->get(
                    $id
                )->getId();
            } catch (NoSuchEntityException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
        return null;
    }
}
