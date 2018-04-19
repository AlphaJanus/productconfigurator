<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 11.04.18
 * Time: 17:56
 */

namespace Netzexpert\ProductConfigurator\Model\ConfiguratorOption\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class OptionType extends AbstractSource implements OptionSourceInterface
{

    const TYPE_TEXT     = 'text';
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Text'), 'value' => self::TYPE_TEXT]
            ];
        }
        return $this->_options;
    }

}