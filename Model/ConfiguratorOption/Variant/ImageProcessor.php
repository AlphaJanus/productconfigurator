<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 14.03.19
 * Time: 13:43
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Variant;

use Magento\Catalog\Model\Product\Gallery\Processor;
use Netzexpert\ProductConfigurator\Controller\Adminhtml\Option\Image\Upload;

class ImageProcessor extends Processor
{
    /**
     * Duplicate temporary images
     *
     * @param string $file
     * @return string
     * @since 101.0.0
     */
    public function duplicateImageFromTmp($file)
    {
        $file = $this->getFilenameFromTmp($file);

        $destinationFile = $this->getUniqueFileName($file, false);
        if ($this->fileStorageDb->checkDbUsage()) {
            $this->fileStorageDb->copyFile(
                $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getTmpMediaShortUrl($file)),
                $this->mediaConfig->getTmpMediaShortUrl($destinationFile)
            );
        } else {
            $this->mediaDirectory->copyFile(
                $this->mediaDirectory->getAbsolutePath(Upload::DESTINATION . $file),
                $this->mediaDirectory->getAbsolutePath(Upload::DESTINATION . $destinationFile)
            );
        }
        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @param bool $forTmp
     * @return string
     * @since 101.0.0
     */
    protected function getUniqueFileName($file, $forTmp = false)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            $destFile = $this->fileStorageDb->getUniqueFilename(
                $this->mediaConfig->getBaseMediaUrlAddition(),
                $file
            );
        } else {
            $destinationFile = $forTmp
                ? $this->mediaDirectory->getAbsolutePath(Upload::DESTINATION . '/tmp/' . $file)
                : $this->mediaDirectory->getAbsolutePath(Upload::DESTINATION . $file);
            $destFile = dirname($file) . '/'
                . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($destinationFile);
        }

        return $destFile;
    }
}
