<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 14.01.19
 * Time: 14:47
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption;

use Magento\Framework\Exception\CouldNotSaveException;
use Netzexpert\ProductConfigurator\Api\ConfiguratorOptionRepositoryInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterface;
use Netzexpert\ProductConfigurator\Api\Data\ConfiguratorOptionInterfaceFactory;

class Copier
{
    /** @var ConfiguratorOptionInterfaceFactory  */
    private $optionFactory;

    /** @var ConfiguratorOptionRepositoryInterface  */
    private $optionRepository;

    /**
     * Copier constructor.
     * @param ConfiguratorOptionInterfaceFactory $optionFactory
     * @param ConfiguratorOptionRepositoryInterface $optionRepository
     */
    public function __construct(
        ConfiguratorOptionInterfaceFactory $optionFactory,
        ConfiguratorOptionRepositoryInterface $optionRepository
    ) {
        $this->optionFactory    = $optionFactory;
        $this->optionRepository = $optionRepository;
    }

    /**
     * @param $option ConfiguratorOptionInterface
     * @return ConfiguratorOptionInterface
     */
    public function copy($option)
    {
        $duplicate = $this->optionFactory->create();
        $optionData = $option->getData();
        $duplicate->setData($optionData);
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalLinkId($option->getId());
        $duplicate->setCreatedAt(null);
        $duplicate->setUpdatedAt(null);
        $duplicate->setId(null);
        $isDuplicateSaved = false;

        do {
            $code = $duplicate->getCode();
            $code = preg_match('/(.*)-(\d+)$/', $code, $matches)
                ? $matches[1] . '-' . ($matches[2] + 1)
                : $code . '-1';
            $duplicate->setCode($code);
            try {
                $this->optionRepository->save($duplicate);
                $isDuplicateSaved = true;
            } catch (CouldNotSaveException $exception) {
            }
        } while (!$isDuplicateSaved);

        return $duplicate;
    }
}
