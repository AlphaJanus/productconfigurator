<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 10.04.18
 * Time: 16:49
 */

namespace Netzexpert\ProductConfigurator\Ui\Component\Listing\Attribute;

class Repository extends AbstractRepository
{
    protected function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
