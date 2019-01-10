<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.12.18
 * Time: 11:40
 */

namespace Netzexpert\ProductConfigurator\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class AbstractModelTest extends TestCase
{

    /** @var ObjectManager */
    protected $objectManager;

    /** @var Context | MockObject */
    protected $context;

    /** @var Registry | MockObject */
    protected $registry;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new ObjectManager($this);
        $this->context = $this->getMock(Context::class);
        $this->registry = $this->getMock(Registry::class);
    }

    protected function getMock($class, $methods = null)
    {
        $mockBuilder =  $this->getMockBuilder($class)
            ->disableOriginalConstructor();
        if (!empty($methods) && is_array($methods)) {
            $mockBuilder->setMethods($methods);
        }
        return $mockBuilder->getMock();
    }

    public function testAfterSave()
    {
    }
}
