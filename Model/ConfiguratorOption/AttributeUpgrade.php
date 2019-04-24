<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 24.04.19
 * Time: 8:39
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Netzexpert\ProductConfigurator\Model\ConfiguratorOption;
use Netzexpert\ProductConfigurator\Setup\ConfiguratorOptionSetup;
use Netzexpert\ProductConfigurator\Setup\ConfiguratorOptionSetupFactory;
use Psr\Log\LoggerInterface;

class AttributeUpgrade implements AttributeUpgradeInterface
{
    /** @var ConfiguratorOptionSetupFactory  */
    private $configuratorOptionSetupFactory;

    /** @var ModuleDataSetupInterface  */
    private $setup;

    /** @var AttributeRepositoryInterface  */
    private $attributeRepository;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * AttributeUpgrade constructor.
     * @param ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory
     * @param ModuleDataSetupInterface $setup
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfiguratorOptionSetupFactory $configuratorOptionSetupFactory,
        ModuleDataSetupInterface $setup,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->configuratorOptionSetupFactory   = $configuratorOptionSetupFactory;
        $this->setup                            = $setup;
        $this->attributeRepository              = $attributeRepository;
        $this->logger                           = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute($attributesData)
    {
        /** @var ConfiguratorOptionSetup $configuratorOptionSetup */
        $configuratorOptionSetup = $this->configuratorOptionSetupFactory->create(['setup' => $this->setup]);
        foreach ($attributesData as $attributeCode => $attributeData) {
            try {
                $attribute = $configuratorOptionSetup->getEavConfig()
                    ->getAttribute(ConfiguratorOption::ENTITY, $attributeCode);
                foreach ($attributeData as $key => $value) {
                    $attribute->setData($key, $value);
                }
                $this->attributeRepository->save($attribute);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
