<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 28.02.19
 * Time: 10:46
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;

class Weight extends Template
{
    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    /** @var Session  */
    private $session;

    /** @var ProductRepositoryInterface  */
    private $productRepository;

    /**
     * Weight constructor.
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $session,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->scopeConfig          = $scopeConfig;
        $this->session              = $session;
        $this->productRepository    = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue(DirectoryHelper::XML_PATH_WEIGHT_UNIT);
    }

    /**
     * @return float|null
     */
    public function getProductWeight()
    {
        $productId = $this->session->getData('last_viewed_product_id');
        if (!$productId) {
            return null;
        }
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
        return $product->getWeight();
    }
}
