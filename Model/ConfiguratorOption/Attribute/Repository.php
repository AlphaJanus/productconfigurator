<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:26
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Attribute;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionAttributeInterface;

class Repository implements \Netzexpert\ProductConfigurator\Api\ConfiguratorOptionAttributeRepositoryInterface
{
    /** @var AttributeRepositoryInterface */
    private $eavAttributeRepository;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /**
     * Repository constructor.
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eavAttributeRepository   = $attributeRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(
            ConfiguratorOptionAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            ConfiguratorOptionAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        return $this->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
