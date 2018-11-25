<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 06.04.18
 * Time: 15:12
 */

namespace Netzexpert\ProductConfigurator\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;

class ConfiguratorOption extends AbstractEntity
{
    /**
     * Entity type getter and lazy loader
     *
     * @return \Magento\Eav\Model\Entity\Type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Netzexpert\ProductConfigurator\Model\ConfiguratorOption::ENTITY);
        }
        return parent::getEntityType();
    }
}
