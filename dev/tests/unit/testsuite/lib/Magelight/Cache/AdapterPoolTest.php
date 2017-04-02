<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Cache;

/**
 * Class AdapterPoolTest
 * @package Magelight\Cache
 */
class AdapterPoolTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Cache\AdapterPool
     */
    protected $adapterPool;

    /**
     * @var \Magelight\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;


    /**
     * Set up before test
     */
    public function setUp()
    {
        $this->configMock = $this->getMockBuilder(\Magelight\Config::class)->disableOriginalConstructor()->getMock();
        \Magelight\Config::forgeMock($this->configMock);
        $this->adapterPool = \Magelight\Cache\AdapterPool::getInstance();
    }

    public function testGetAdapterClassByType()
    {
        $this->assertEquals(
            '\Magelight\Cache\Adapter\Dummy',
            $this->adapterPool->getAdapterClassByType('dummy')
        );
    }

    public function testGetAdapter()
    {
        $cacheConfig = new \SimpleXMLElement('<default>
                <type>file</type>
                <config>
                    <path>var/cache</path>
                </config>
            </default>
        ');
        $this->configMock->expects($this->once())
            ->method('getConfig')
            ->with('global/cache/default')
            ->will($this->returnValue($cacheConfig));

        $fileAdapterMock = $this->getMockBuilder(\Magelight\Cache\Adapter\File::class)
            ->disableOriginalConstructor()
            ->getMock();
        \Magelight\Cache\Adapter\File::forgeMock($fileAdapterMock);

        $this->assertEquals($fileAdapterMock, $this->adapterPool->getAdapter());
    }
}
