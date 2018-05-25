<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 17.05.18
 * Time: 15:30
 */

namespace Netzexpert\ProductConfigurator\Controller\Adminhtml\Option\Image;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class Upload extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netzexpert_ProductConfigurator::options';

    const DESTINATION = 'configurator/option';

    /** @var UploaderFactory  */
    private $uploaderFactory;

    /** @var Filesystem  */
    private $filesystem;

    /** @var StoreManagerInterface  */
    private $storeManager;

    private $allowedExtensions = ['jpg','jpeg','png','gif','csv'];

    public function __construct(
        Action\Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory  = $uploaderFactory;
        $this->filesystem       = $filesystem;
        $this->storeManager     = $storeManager;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $target = $this->getDestinationPath();
            $uploader = $this->uploaderFactory->create(['fileId' => 'optionImage']);
            $uploader->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setFilesDispersion(true)
                ->setAllowedExtensions($this->allowedExtensions);
            $result = $uploader->save($target);
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('File can not be saved to the destination folder.')
                );
            }
            if ($result) {
                unset($result['path']);
                $result['url'] = $this->storeManager->getStore()
                                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::DESTINATION . $result['file'];
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addExceptionMessage($exception);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function getDestinationPath()
    {
        try {
            $directory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            return $directory->getAbsolutePath(self::DESTINATION);
        } catch (FileSystemException $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }
        throw new LocalizedException(__('Can\'t write to folder'));
    }
}
