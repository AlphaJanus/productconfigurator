<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netzexpert\ProductConfigurator\Setup\Patch\Data;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\ProductConfiguratorOptionsVariantRepositoryInterface;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOption;
use Netzexpert\ProductConfigurator\Model\Product\ProductConfiguratorOptionVariant;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOption\CollectionFactory
    as ProductConfiguratorOptionCollectionFactory;
use Netzexpert\ProductConfigurator\Model\ResourceModel\Product\ProductConfiguratorOptionVariant\CollectionFactory
    as ProductConfiguratorOptionVariantsCollectionFactory;
use Psr\Log\LoggerInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class UpdateProductConfiguratorOptionsDependencies implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var ProductConfiguratorOptionCollectionFactory  */
    private $collectionFactory;

    /** @var ProductConfiguratorOptionVariantsCollectionFactory  */
    private $variantsCollectionFactory;

    /** @var ProductConfiguratorOptionRepositoryInterface  */
    private $configuratorOptionRepository;

    /** @var ProductConfiguratorOptionsVariantRepositoryInterface  */
    private $variantRepository;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * UpdateProductConfiguratorOptionsDependencies constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ProductConfiguratorOptionCollectionFactory $collectionFactory
     * @param ProductConfiguratorOptionVariantsCollectionFactory $variantsCollectionFactory
     * @param ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository
     * @param ProductConfiguratorOptionsVariantRepositoryInterface $variantRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductConfiguratorOptionCollectionFactory $collectionFactory,
        ProductConfiguratorOptionVariantsCollectionFactory $variantsCollectionFactory,
        ProductConfiguratorOptionRepositoryInterface $configuratorOptionRepository,
        ProductConfiguratorOptionsVariantRepositoryInterface $variantRepository,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup              = $moduleDataSetup;
        $this->collectionFactory            = $collectionFactory;
        $this->variantsCollectionFactory    = $variantsCollectionFactory;
        $this->configuratorOptionRepository = $configuratorOptionRepository;
        $this->variantRepository            = $variantRepository;
        $this->logger                       = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.19';
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $optionCollection = $this->collectionFactory->create();
        /** @var ProductConfiguratorOption $option */
        foreach ($optionCollection as $option) {
            if ((int) $option->getParentOption() == 0) {
                $option->setParentOption(null);
            }
            $option->setDependencies([]);
            try {
                $this->configuratorOptionRepository->save($option);
            } catch (CouldNotSaveException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        $variantsCollection = $this->variantsCollectionFactory->create();
        /** @var ProductConfiguratorOptionVariant $variant */
        foreach ($variantsCollection as $variant) {
            $variant->setDependencies([]);
            try {
                $this->variantRepository->save($variant);
            } catch (CouldNotSaveException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            AddExtensionsAttribute::class
        ];
    }
}
