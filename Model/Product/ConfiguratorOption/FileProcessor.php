<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 22.01.19
 * Time: 9:58
 */

namespace Netzexpert\ProductConfigurator\Model\Product\ConfiguratorOption;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Size;
use Magento\Framework\Filesystem;
use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Validator\File\IsImage;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ProductConfiguratorOptionInterface;

class FileProcessor
{
    /** @var FileTransferFactory  */
    private $fileTransferFactory;

    /** @var Filesystem */
    private $filesystem;

    /** @var Filesystem\Directory\WriteInterface  */
    private $mediaDirectory;

    /** @var Size */
    private $fileSize;

    /** @var IsImage  */
    private $isImageValidator;

    /** @var Random  */
    private $random;

    /**
     * Relative path for main destination folder
     *
     * @var string
     */
    private $path = 'configurator_options';

    /**
     * Relative path for quote folder
     *
     * @var string
     */
    protected $quotePath = 'configurator_options/quote';

    /**
     * Relative path for order folder
     *
     * @var string
     */
    protected $orderPath = 'configurator_options/order';

    /**
     * FileValidator constructor.
     * @param FileTransferFactory $fileTransferFactory
     * @param Filesystem $filesystem
     * @param Size $fileSize
     * @param IsImage $isImageValidator
     * @param Random $random
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        FileTransferFactory $fileTransferFactory,
        Filesystem $filesystem,
        Size $fileSize,
        IsImage $isImageValidator,
        Random $random
    ) {
        $this->fileTransferFactory  = $fileTransferFactory;
        $this->filesystem           = $filesystem;
        $this->mediaDirectory       = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileSize             = $fileSize;
        $this->isImageValidator     = $isImageValidator;
        $this->random               = $random;
    }

    /**
     * @param $option ProductConfiguratorOptionInterface
     * @param $optionEntity ConfiguratorOptionInterface
     * @param $product Product
     * @throws Exception
     * @throws LocalizedException
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate($option, $optionEntity, $product)
    {
        $upload = $this->fileTransferFactory->create();
        $file = 'configurator_options_' . $option->getId() . '_file';
        $valid = ($optionEntity->getData('is_required') == "0" ||
            ($optionEntity->getData('is_required') == '1' && !$upload->isUploaded($file)));
        if (!$valid) {
            throw new Exception(
                __('Validation failed. Required options were not filled or the file was not uploaded.')
            );
        }
        $fileInfo = $upload->getFileInfo($file)[$file];
        $fileInfo['title'] = $fileInfo['name'];
        // when file exceeds the upload_max_filesize, $_FILES is empty
        if ($this->validateContentLength()) {
            $value = $this->fileSize->getMaxFileSizeInMb();
            throw new LocalizedException(
                __('The file you uploaded is larger than %1 Megabytes allowed by server', $value)
            );
        }

        try {
            // File extension
            $allowed = $this->parseExtensionsString($optionEntity->getData('extensions'));
            if ($allowed !== null) {
                $upload->addValidator(new \Zend_Validate_File_Extension($allowed));
            }

            $upload->addValidator(
                new \Zend_Validate_File_FilesSize(['max' => $this->fileSize->getMaxFileSize()])
            );
        } catch (\Zend_File_Transfer_Exception $exception) {
            throw new Exception(
                __('Your file doesn\'t match allowed extensions')
            );
        } catch (\Zend_Validate_Exception $exception) {
            throw new LocalizedException(
                __(
                    'The file you uploaded is larger than %1 Megabytes allowed by server',
                    $this->fileSize->getMaxFileSizeInMb()
                )
            );
        }

        /**
         * Upload process
         */
        $this->initFilesystem();
        $userValue = [];

        if ($upload->isUploaded($file) && $upload->isValid($file)) {
            $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($fileInfo['name']);
            $dispersion = \Magento\MediaStorage\Model\File\Uploader::getDispersionPath($fileName);
            $filePath = $dispersion;

            $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
            $fileHash = md5($tmpDirectory->readFile($tmpDirectory->getRelativePath($fileInfo['tmp_name'])));
            $fileRandomName = $this->random->getRandomString(32);
            $filePath .= '/' .$fileRandomName;
            $fileFullPath = $this->mediaDirectory->getAbsolutePath($this->quotePath . $filePath);
            try {
                $upload->addFilter(new \Zend_Filter_File_Rename(['target' => $fileFullPath, 'overwrite' => true]));
            } catch (\Zend_Filter_Exception $exception) {
                throw new LocalizedException(__($exception->getMessage()));
            } catch (\Zend_File_Transfer_Exception $exception) {
                throw new LocalizedException(__($exception->getMessage()));
            }

            if ($product !== null) {
                $product->getTypeInstance()->addFileQueue(
                    [
                        'operation' => 'receive_uploaded_file',
                        'src_name' => $file,
                        'dst_name' => $fileFullPath,
                        'uploader' => $upload,
                        'option' => $this,
                    ]
                );
            }

            $_width = 0;
            $_height = 0;

            if ($tmpDirectory->isReadable($tmpDirectory->getRelativePath($fileInfo['tmp_name']))) {
                if (filesize($fileInfo['tmp_name'])) {
                    if ($this->isImageValidator->isValid($fileInfo['tmp_name'])) {
                        $imageSize = getimagesize($fileInfo['tmp_name']);
                    }
                } else {
                    throw new LocalizedException(__('The file is empty. Please choose another one'));
                }

                if (!empty($imageSize)) {
                    $_width = $imageSize[0];
                    $_height = $imageSize[1];
                }
            }

            $userValue = [
                'type' => $fileInfo['type'],
                'title' => $fileInfo['name'],
                'quote_path' => $this->quotePath . $filePath,
                'order_path' => $this->orderPath . $filePath,
                'fullpath' => $fileFullPath,
                'size' => $fileInfo['size'],
                'width' => $_width,
                'height' => $_height,
                'secret_key' => substr($fileHash, 0, 20),
            ];
        }

        if ($upload->getErrors()) {
            $errors = $this->getValidatorErrors($upload->getErrors(), $fileInfo, $optionEntity);

            if (count($errors) > 0) {
                throw new LocalizedException(__(implode("\n", $errors)));
            }
        }

        return $userValue;
    }

    /**
     * @return bool
     */
    private function validateContentLength()
    {
        return isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > $this->fileSize->getMaxFileSize();
    }

    /**
     * Parse file extensions string with various separators
     *
     * @param string $extensions String to parse
     * @return array|null
     */
    private function parseExtensionsString($extensions)
    {
        if (preg_match_all('/(?<extension>[a-z0-9]+)/si', strtolower($extensions), $matches)) {
            return $matches['extension'] ?: null;
        }
        return null;
    }

    /**
     * Directory structure initializing
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return void
     */
    private function initFilesystem()
    {
        $this->mediaDirectory->create($this->path);
        $this->mediaDirectory->create($this->quotePath);
        $this->mediaDirectory->create($this->orderPath);

        // Directory listing and hotlink secure
        $path = $this->path . '/.htaccess';
        if (!$this->mediaDirectory->isFile($path)) {
            $this->mediaDirectory->writeFile($path, "Order deny,allow\nDeny from all");
        }
    }

    /**
     * Get Error messages for validator Errors
     *
     * @param string[] $errors Array of validation failure message codes @see \Zend_Validate::getErrors()
     * @param array $fileInfo File info
     * @param ConfiguratorOptionInterface $option
     * @return string[] Array of error messages
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_getValidatorErrors
     */
    private function getValidatorErrors($errors, $fileInfo, $option)
    {
        $result = [];
        foreach ($errors as $errorCode) {
            switch ($errorCode) {
                case \Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION:
                    $result[] = __(
                        "The file '%1' for '%2' has an invalid extension.",
                        $fileInfo['title'],
                        $option->getName()
                    );
                    break;
                case \Zend_Validate_File_Extension::FALSE_EXTENSION:
                    $result[] = __(
                        "The file '%1' for '%2' has an invalid extension.",
                        $fileInfo['title'],
                        $option->getName()
                    );
                    break;
                case \Zend_Validate_File_FilesSize::TOO_BIG:
                    $result[] = __(
                        "The file '%1' you uploaded is larger than the %2 megabytes allowed by our server.",
                        $fileInfo['title'],
                        $this->fileSize->getMaxFileSizeInMb()
                    );
                    break;
                case \Zend_Validate_File_ImageSize::NOT_DETECTED:
                    $result[] = __(
                        "The file '%1' is empty. Please choose another one",
                        $fileInfo['title']
                    );
                    break;
                default:
                    $result[] = __(
                        "The file '%1' is invalid. Please choose another one",
                        $fileInfo['title']
                    );
            }
        }
        return $result;
    }
}
