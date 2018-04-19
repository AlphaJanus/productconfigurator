<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:41
 */

namespace Netzexpert\ProductConfigurator\Ui\Component\Listing\Attribute;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{

    /**
     * @var null|\Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface[]
     */
    private $attributes;

    /** @var ConfiguratorOptionAttributeRepositoryInterface  */
    private $configuratorOptionAttributeRepository;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;
    /**
     * AbstractRepository constructor.
     * @param ConfiguratorOptionAttributeRepositoryInterface $configuratorOptionAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ConfiguratorOptionAttributeRepositoryInterface $configuratorOptionAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->configuratorOptionAttributeRepository    = $configuratorOptionAttributeRepository;
        $this->searchCriteriaBuilder                    = $searchCriteriaBuilder;
    }

    abstract protected function buildSearchCriteria();

    /**
     * @return \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface[]
     */
    public function getList()
    {
        if (null == $this->attributes) {
            $this->attributes = $this->configuratorOptionAttributeRepository
                ->getList($this->buildSearchCriteria())
                ->getItems();
        }
        return $this->attributes;
    }
}
