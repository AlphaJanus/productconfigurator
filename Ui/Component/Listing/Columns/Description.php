<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 20.12.18
 * Time: 17:48
 */

namespace Netzexpert\ProductConfigurator\Ui\Component\Listing\Columns;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Description extends Column
{
    /** @var FilterProvider  */
    private $filterProvider;

    /**
     * Description constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterProvider $filterProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterProvider $filterProvider,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                try {
                    $item[$this->getData('name')] = $this->filterProvider->getPageFilter()
                        ->filter($item[$this->getData('name')]);
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }
        return parent::prepareDataSource($dataSource);
    }

}
