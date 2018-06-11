<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.06.18
 * Time: 17:55
 */

namespace Netzexpert\ProductConfigurator\Block\Adminhtml\ConfiguratorOption\Edit;

use Magento\Framework\View\Element\Template;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Collection;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\CollectionFactory;

class ExpressionHint extends \Magento\Framework\View\Element\Template
{

    /** @var CollectionFactory  */
    private $collectionFactory;

    /**
     * ExpressionHint constructor.
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return Collection
     */
    public function getOptions()
    {
        $collection = $this->collectionFactory->create();
        if ($currentId = $this->getData('current_id')) {
            $collection->addFieldToFilter('entity_id', ['neq' => $currentId]);
        }
        return $collection;
    }
}
