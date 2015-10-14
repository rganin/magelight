<?php

namespace Magelight\Cache;

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


    public function setUp()
    {
        $this->configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
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

        $fileAdapterMock = $this->getMock(\Magelight\Cache\Adapter\File::class, [], [], '', false);
        \Magelight\Cache\Adapter\File::forgeMock($fileAdapterMock);

        $this->assertEquals($fileAdapterMock, $this->adapterPool->getAdapter());
    }
}
