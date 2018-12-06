<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 17:15
 */

namespace Netzexpert\ProductConfigurator\Ui\Component;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentInterface;
use Psr\Log\LoggerInterface;

class ColumnFactory
{
    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    private $componentFactory;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * @var array
     */
    private $jsComponentMap = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    private $dataTypeMap = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'multiselect',
        'date' => 'date',
    ];

    /**
     * @param \Magento\Framework\View\Element\UiComponentFactory $componentFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponentFactory $componentFactory,
        LoggerInterface $logger
    ) {
        $this->componentFactory = $componentFactory;
        $this->logger           = $logger;
    }

    /**
     * @param Attribute $attribute
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param array $config
     * @return UiComponentInterface
     */
    public function create($attribute, $context, array $config = [])
    {
        $columnName = $attribute->getAttributeCode();
        $config = array_merge([
            'label' => __($attribute->getDefaultFrontendLabel()),
            'dataType' => $this->getDataType($attribute),
            'add_field' => true,
            'visible' => $attribute->getData('is_visible_in_grid'),
            'filter' => ($attribute->getData('is_filterable_in_grid'))
                ? $this->getFilterType($attribute->getFrontendInput())
                : null,
        ], $config);

        if ($attribute->usesSource()) {
            try {
                $config['options'] = $attribute->getSource()->getAllOptions();
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        $config['component'] = $this->getJsComponent($config['dataType']);

        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        try {
            return $this->componentFactory->create($columnName, 'column', $arguments);
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
            return null;
        }
    }

    /**
     * @param string $dataType
     * @return string
     */
    private function getJsComponent($dataType)
    {
        return $this->jsComponentMap[$dataType];
    }

    /**
     * @param Attribute $attribute
     * @return string
     */
    private function getDataType($attribute)
    {
        return isset($this->dataTypeMap[$attribute->getFrontendInput()])
            ? $this->dataTypeMap[$attribute->getFrontendInput()]
            : $this->dataTypeMap['default'];
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    private function getFilterType($frontendInput)
    {
        $filtersMap = ['date' => 'dateRange'];
        $result = array_replace_recursive($this->dataTypeMap, $filtersMap);
        return isset($result[$frontendInput]) ? $result[$frontendInput] : $result['default'];
    }
}
