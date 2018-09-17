<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.08.18
 * Time: 10:37
 */

namespace Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\Type;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image as ImageHelper;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Block\Product\View\ConfiguratorOptions\AbstractOptions;

class Image extends AbstractOptions
{

    private $filesystem;

    private $imageFactory;

    private $imageHelper;

    public function __construct(
        Template\Context $context,
        ConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        Json $json,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->filesystem   = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->imageHelper  = $imageHelper;
        parent::__construct($context, $configuratorOptionRepository, $json, $data);
    }

    public function getResizedImage($value, $width, $height = null)
    {
        if(!$height){
            $height = $width;
        }
        $resizePath = 'configurator/option/resized/' . $width . 'x' . $height;
        $destination = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($resizePath) . $value['image'];
        if(!is_file($destination)) {
            $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath('configurator/option') . $value['image'];

            $imageResize = $this->imageFactory->create();
            try {
                $imageResize->open($absolutePath);
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
                return $this->imageHelper->getDefaultPlaceholderUrl('small_image');
            }
            $imageResize->constrainOnly(false);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $imageResize->save($destination);
        }
        return $this->_storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $resizePath . $value['image'];
    }
}
