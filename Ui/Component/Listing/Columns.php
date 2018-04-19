<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:13
 */

namespace Netzexpert\ProductConfigurator\Ui\Component\Listing;

use Netzexpert\ProductConfigurator\Ui\Component\ColumnFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Netzexpert\ProductConfigurator\Ui\Component\Listing\Attribute\RepositoryInterface;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /** @var RepositoryInterface  */
    private $attributeRepository;

    /** @var ColumnFactory  */
    private $columnFactory;

    /**
     * @var array
     */
    private $filterMap = [
        'default' => 'text',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'select',
        'date' => 'dateRange',
    ];

    /**
     * Columns constructor.
     * @param ContextInterface $context
     * @param RepositoryInterface $attributeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ColumnFactory $columnFactory,
        RepositoryInterface $attributeRepository,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->columnFactory        = $columnFactory;
        $this->attributeRepository  = $attributeRepository;
    }

    public function prepare()
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($this->attributeRepository->getList() as $attribute) {
            $config = [];
            if (!isset($this->components[$attribute->getAttributeCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                if ($attribute->getIsFilterableInGrid()) {
                    $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                }
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
        parent::prepare();
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    private function getFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }
}
