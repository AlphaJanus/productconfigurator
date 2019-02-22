<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.05.18
 * Time: 15:18
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractExtensibleModel;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionVariantInterface;
use Netzexpert\ProductConfigurator\Model\ResourceModel\ConfiguratorOption\Variant as VariantResource;

class Variant extends AbstractExtensibleModel implements ConfiguratorOptionVariantInterface, IdentityInterface
{
    const CACHE_TAG = 'configurator_option_variant';

    private $eventPrefix = 'configurator_option_variant';

    /** @var Filesystem  */
    private $filesystem;

    /** @var File  */
    private $file;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        VariantResource $resource,
        Filesystem $filesystem,
        File $file,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filesystem   = $filesystem;
        $this->file         = $file;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function getEventPrefix()
    {
        return $this->eventPrefix;
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getConfiguratorOptionId()
    {
        return $this->getData(self::CONFIGURATOR_OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setConfiguratorOptionId($configuratorOptionId)
    {
        return $this->setData(self::CONFIGURATOR_OPTION_ID, $configuratorOptionId);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * @inheritDoc
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getShowInCart()
    {
        return $this->getData(self::SHOW_IN_CART);
    }

    /**
     * @inheritDoc
     */
    public function setShowInCart($showInCart)
    {
        return $this->setData(self::SHOW_IN_CART, $showInCart);
    }

    /**
     * Remove old image after save
     * @inheritDoc
     */
    public function afterSave()
    {
        $image = $this->getOrigData('image');
        if ($image && $image != $this->getImage()) {
            $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $filePath = $mediaDir->getAbsolutePath('configurator/option');
            try {
                if ($this->file->isExists($filePath . $image)) {
                    $this->file->deleteFile($filePath . $image);
                }
            } catch (FileSystemException $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }
        return parent::afterSave();
    }

    /**
     * Remove old image after delete
     * @inheritDoc
     */
    public function afterDelete()
    {
        $image = $this->getImage();
        if ($image) {
            $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $filePath = $mediaDir->getAbsolutePath('configurator/option');
            try {
                if ($this->file->isExists($filePath . $image)) {
                    $this->file->deleteFile($filePath . $image);
                }
            } catch (FileSystemException $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }
        return parent::afterDelete();
    }
}
