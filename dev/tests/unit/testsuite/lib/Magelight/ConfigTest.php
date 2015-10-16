<?php

namespace Magelight;

class ConfigTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Config
     */
    protected $config;

    /**
     * @var \Magelight\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appMock;

    /**
     * @var \Magelight\Components\Loaders\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \Magelight\Components\Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesMock;

    /**
     * @var \Magelight\Cache\AdapterPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheAdapterPoolMock;

    /**
     * @var \Magelight\Cache\AdapterAbstract|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheAdapterMock;

    public function setUp()
    {
        $this->appMock = $this->getMock(\Magelight\App::class, [], [], '', false);
        \Magelight\App::forgeMock($this->appMock);

        $this->configLoaderMock = $this->getMock(\Magelight\Components\Loaders\Config::class, ['getConfig', 'loadConfig', 'getModulesConfigFilePath'], [], '', false);
        \Magelight\Components\Loaders\Config::forgeMock($this->configLoaderMock);

        $this->modulesMock = $this->getMock(\Magelight\Components\Modules::class, [], [], '', false);
        \Magelight\Components\Modules::forgeMock($this->modulesMock);

        $this->cacheAdapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($this->cacheAdapterPoolMock);

        $this->cacheAdapterMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            []
        );

        $this->cacheAdapterPoolMock->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->cacheAdapterMock));

        $this->config = \Magelight\Config::forge();
    }

    public function testLoadFromCache()
    {
        $appConfig = '<config><global>
            <app>
                <cache_modules_config>1</cache_modules_config>
            </app>
        </global></config>';

        $cachedModulesConfigString = '<config><global>
            <app>
                <cache_modules_config>0</cache_modules_config>
                <developer_mode>1</developer_mode>
            </app>
        </global></config>';
        $this->cacheAdapterMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($cachedModulesConfigString));

        $this->appMock->expects($this->once())
            ->method('getAppDir')
            ->will($this->returnValue('/app'));

        $this->configLoaderMock->expects($this->once())
            ->method('loadConfig')
            ->with('/app' . DS . 'etc' . DS . 'config.xml');

        $this->configLoaderMock->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue(new \SimpleXMLElement($appConfig)));

        $this->config->load();
        $this->assertEquals(1, $this->config->getConfigInt('/global/app/developer_mode'));
        $this->assertEquals(0, $this->config->getConfigInt('/global/app/cache_modules_config'));
    }

    public function testLoadFromCacheMiss()
    {
        $appConfig = '<config><global>
            <app>
                <cache_modules_config>1</cache_modules_config>
            </app>
        </global></config>';

        $expectedConfig = new \SimpleXMLElement('<config><global>
            <app>
                <cache_modules_config>0</cache_modules_config>
                <framework_config_data>framework</framework_config_data>
                <app_module_config_data>app.domain</app_module_config_data>
                <bool_data_true>1</bool_data_true>
                <bool_data_false>0</bool_data_false>
                <int_data>123</int_data>
                <float_data>123.456</float_data>
                <array_data>
                    <node_1>data</node_1>
                    <node_2>data_node_2</node_2>
                </array_data>
                <string_data>some string</string_data>
                <config_with_attribute attribute_1="attribute_value">config_string</config_with_attribute>
            </app>
        </global></config>');

        $this->appMock->expects($this->once())
            ->method('getAppDir')
            ->will($this->returnValue('/app'));

        $this->appMock->expects($this->once())
            ->method('getModuleDirectories')
            ->will($this->returnValue(['/app/modules', '/framework/modules']));

        $this->configLoaderMock->expects($this->at(0))
            ->method('loadConfig')
            ->with('/app' . DS . 'etc' . DS . 'config.xml');

        $this->configLoaderMock->expects($this->at(1))
            ->method('getConfig')
            ->will($this->returnValue(new \SimpleXMLElement($appConfig)));

        $this->modulesMock->expects($this->any())
            ->method('getActiveModules')
            ->will(
                $this->returnValue(
                    [
                        ['name' => 'Magelight_Module', 'path' => 'Magelight\Module'],
                        ['name' => 'App_Module', 'path' => 'App\Module'],
                    ]
                )
            );

        $this->configLoaderMock->expects($this->any())
            ->method('getModulesConfigFilePath')
            ->will($this->returnValueMap(
                [
                    [
                        '/framework/modules',
                        ['name' => 'Magelight_Module', 'path' => 'Magelight\Module'],
                        '/framework/modules/Magelight\Module' . DS . 'etc' . DS . 'config.xml'
                    ],
                    [
                        '/app/modules',
                        ['name' => 'Magelight_Module', 'path' => 'Magelight\Module'],
                        null
                    ],
                    [
                        '/framework/modules',
                        ['name' => 'App_Module', 'path' => 'App\Module'],
                        null
                    ],
                    [
                        '/app/modules',
                        ['name' => 'App_Module', 'path' => 'App\Module'],
                        '/app/modules/App\Module' . DS . 'etc' . DS . 'config.xml'
                    ]

                ]
            ));

        $this->configLoaderMock->expects($this->at(3))
            ->method('loadConfig')
            ->with('/framework/modules/Magelight\Module' . DS . 'etc' . DS . 'config.xml');

        $this->configLoaderMock->expects($this->at(7))
            ->method('loadConfig')
            ->with('/app/modules/App\Module' . DS . 'etc' . DS . 'config.xml');

        $this->configLoaderMock->expects($this->at(8))
            ->method('getConfig')
            ->will($this->returnValue($expectedConfig));

        $this->cacheAdapterMock->expects($this->once())
            ->method('set')
            ->with($this->config->buildCacheKey('modules_config'), $expectedConfig->asXML(), 3600);

        $this->config->load();

        $this->assertEquals('framework', $this->config->getConfigString('/global/app/framework_config_data'));
        $this->assertEquals('app.domain', $this->config->getConfigString('/global/app/app_module_config_data'));
        $this->assertTrue($this->config->getConfigBool('/global/app/bool_data_true'));
        $this->assertFalse($this->config->getConfigBool('/global/app/bool_data_false'));

        $this->assertInternalType('int', $this->config->getConfigInt('/global/app/int_data'));
        $this->assertEquals(123, $this->config->getConfigInt('/global/app/int_data'));

        $this->assertInternalType('float', $this->config->getConfigFloat('/global/app/float_data'));
        $this->assertEquals(123.456, $this->config->getConfigFloat('/global/app/float_data'));

        $this->assertInternalType('array', $this->config->getConfigArray('/global/app/array_data'));
        $this->assertEquals(
            ['node_1' => 'data', 'node_2' => 'data_node_2'],
            $this->config->getConfigArray('/global/app/array_data')
        );

        $this->assertInternalType('string', $this->config->getConfigString('/global/app/string_data'));
        $this->assertEquals('some string', $this->config->getConfigString('/global/app/string_data'));

        $this->assertInternalType(
            'string',
            $this->config->getConfigAttribute('/global/app/config_with_attribute', 'attribute_1')
        );
        $this->assertEquals(
            'attribute_value',
            $this->config->getConfigAttribute('/global/app/config_with_attribute', 'attribute_1')
        );

        $this->assertInternalType(
            'string',
            $this->config->getConfigString('/global/app/unexistent_node', 'default_value')
        );
        $this->assertEquals(
            'default_value',
            $this->config->getConfigString('/global/app/unexistent_node', 'default_value')
        );

        $this->assertInternalType('array', $this->config->getConfigSet('/global/app'));

        $this->assertInternalType('string', $this->config->getConfigXmlString());
        $this->assertEquals($expectedConfig->asXML(), $this->config->getConfigXmlString());
    }
}
