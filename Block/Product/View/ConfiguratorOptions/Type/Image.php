<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:37
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class Image extends AbstractOptions
{

    /** @var Filesystem */
    private $filesystem;

    /** @var AdapterFactory */
    private $imageFactory;

    /** @var ImageHelper */
    private $imageHelper;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * Image constructor.
     * @param Template\Context $context
     * @param ConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterProvider $filterProvider
     * @param Json $json
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param ImageHelper $imageHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionRepositoryInterface $productConfiguratorOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterProvider $filterProvider,
        Json $json,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        ImageHelper $imageHelper,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->filesystem   = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->imageHelper  = $imageHelper;
        $this->scopeConfig  = $scopeConfig;
        parent::__construct(
            $context,
            $configuratorOptionRepository,
            $productConfiguratorOptionRepository,
            $searchCriteriaBuilder,
            $filterProvider,
            $json,
            $data
        );
    }

    public function getResizedImage($value, $width, $height = null)
    {
        if (!$height) {
            $height = $width;
        }
        $resizePath = 'configurator/option/resized/' . $width . 'x' . $height;
        $destination = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($resizePath) . $value['image'];
        if (!$this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->isFile($destination)) {
            $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath('configurator/option') . $value['image'];

            $imageResize = $this->imageFactory->create();
            try {
                $imageResize->open($absolutePath);
                $imageResize->constrainOnly(false);
                $imageResize->keepTransparency(true);
                $imageResize->keepFrame(false);
                $imageResize->keepAspectRatio(true);
                $imageResize->resize($width, $height);
                $imageResize->save($destination);
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
                return $this->imageHelper->getDefaultPlaceholderUrl('small_image');
            }
        }
        return $this->_storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $resizePath . $value['image'];
    }

    public function getDefaultValue()
    {
        $configuredValue = $this->getProduct()
            ->getPreconfiguredValues()
            ->getData('configurator_options/' . $this->getOption()->getId());
        if ($configuredValue) {
            return $configuredValue;
        }
        $default = null;
        $fistActive = null;
        foreach ($this->getValuesData() as $value) {
            if ($value['enabled'] && !$fistActive) {
                $fistActive = $value['value_id'];
            }
            if ($value['is_default'] && $value['enabled']) {
                $default = $value['value_id'];
            }
        }
        return ($default) ? $default : $fistActive;
    }

    /**
     * @param string $image
     * @return string
     */
    public function getImageUrl($image)
    {
        if ($image === null) {
            $image = $this->scopeConfig->getValue(
                'catalog/placeholder/image_placeholder',
                ScopeInterface::SCOPE_STORE
            );
            if (!$image) {
                return '#';
            }
        }
        try {
            /** @var Store $store */
            $store = $this->_storeManager->getStore();

            return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'configurator/option' . $image;
        } catch (NoSuchEntityException $exception) {
            $this->_logger->error($exception->getMessage());
            return '#';
        }
    }
}
