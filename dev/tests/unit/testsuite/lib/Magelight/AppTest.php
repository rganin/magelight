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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Magelight;


class AppTest extends \Magelight\TestCase
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var \Magelight\Components\Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesMock;

    /**
     * @var \Magelight\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magelight\Components\Router|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;

    /**
     * @var \Magelight\Http\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magelight\I18n\Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var \Magelight\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * Set up before test
     */
    public function setUp()
    {
        $this->app = $this->getMockForAbstractClass(App::class, [], '', false, false, true, []);

        $this->modulesMock = $this->getMock(\Magelight\Components\Modules::class, [], [], '', false);
        $this->configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
        $this->routerMock = $this->getMock(\Magelight\Components\Router::class, [], [], '', false);
        $this->sessionMock = $this->getMock(\Magelight\Http\Session::class, [], [], '', false);
        $this->translatorMock = $this->getMock(\Magelight\I18n\Translator::class, [], [], '', false);
        $this->eventManagerMock = $this->getMock(\Magelight\Event\Manager::class, [], [], '', false);

        \Magelight\Components\Modules::forgeMock($this->modulesMock);
        \Magelight\Config::forgeMock($this->configMock);
        \Magelight\Components\Router::forgeMock($this->routerMock);
        \Magelight\Http\Session::forgeMock($this->sessionMock);
        \Magelight\I18n\Translator::forgeMock($this->translatorMock);
        \Magelight\Event\Manager::forgeMock($this->eventManagerMock);
    }

    public function testGeneric()
    {
        $this->assertTrue($this->app->setAppDir('') instanceof App);
        $this->assertTrue($this->app->setDeveloperMode(true) instanceof App);
        $this->assertTrue($this->app->setSessionCookieName('SESSION') instanceof App);
        $this->assertTrue(is_string($this->app->getSessionCookieName()));
        $this->assertTrue($this->app->getSessionCookieName() === 'SESSION');
    }

    public function testSetFrameworkDir()
    {
        $this->app->setFrameworkDir('framework/dir');
        $this->assertEquals('framework/dir', $this->app->getFrameworkDir());
    }

    public function testSetAppDir()
    {
        $this->app->setAppDir('app/dir');
        $this->assertEquals('app/dir', $this->app->getAppDir());
    }

    public function testSetDeveloperMode()
    {
        $this->app->setDeveloperMode(true);
        $this->assertEquals(true, $this->app->isInDeveloperMode());
        $this->app->setDeveloperMode(false);
        $this->assertEquals(false, $this->app->isInDeveloperMode());
    }

    public function testSetSettionCookieName()
    {
        $this->assertEquals(App::SESSION_ID_COOKIE_NAME, $this->app->getSessionCookieName());
        $this->app->setSessionCookieName('sessioncookiename');
        $this->assertEquals('sessioncookiename', $this->app->getSessionCookieName());
    }

    public function testInit()
    {
        $this->sessionMock->expects($this->once())->method('setSessionName')->will($this->returnSelf());
        $this->translatorMock->expects($this->once())->method('loadTranslations');
        $this->app->init();
        $this->assertEquals(\Magelight\App::DEFAULT_LANGUAGE, $this->app->getLang());
    }

    public function testDbMysql()
    {
        $mysqlConfig = new \SimpleXMLElement(
            '<config>
                <type>mysql</type>
                <host>localhost</host>
            </config>'
        );
        $index = \Magelight\App::DEFAULT_INDEX;
        $mysqlDbAdapterMock = $this->getMock(\Magelight\Db\Mysql\Adapter::class, ['init'], [], '', false);
        $mysqlDbAdapterMock->expects($this->once())
            ->method('init')
            ->with((array)$mysqlConfig);

        \Magelight\Db\Mysql\Adapter::forgeMock($mysqlDbAdapterMock);
        $this->configMock->expects($this->once())
            ->method('getConfig')
            ->with('/global/db/' . $index, null)
            ->will($this->returnValue($mysqlConfig));

        $this->assertInstanceOf(\Magelight\Db\Mysql\Adapter::class, $this->app->db());
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Database `default` configuration not found.
     */
    public function testDbEmptyConfig()
    {
        $mysqlConfig = null;
        $index = \Magelight\App::DEFAULT_INDEX;
        $this->configMock->expects($this->once())
            ->method('getConfig')
            ->with('/global/db/' . $index, null)
            ->will($this->returnValue($mysqlConfig));

        $this->app->db();
    }

    public function testDispatchAction()
    {
        $action = ['module' => 'Magelight', 'controller' => 'controller', 'action' => 'index'];
        $requestMock = $this->getMock(\Magelight\Http\Request::class, [], [], '', false);
        \Magelight\Http\Request::forgeMock($requestMock);

        class_alias(\Magelight\Controller::class, '\Magelight\Controllers\Controller');
        $controllerMock = $this->getMockForAbstractClass(
            \Magelight\Controller::class,
            [],
            '',
            false,
            false,
            false,
            ['indexAction', 'init', 'beforeExecute', 'afterExecute']
        );
        \Magelight\Controller::forgeMock($controllerMock);

        $controllerMock->expects($this->once())
            ->method('indexAction');
        $controllerMock->expects($this->once())
            ->method('init')->with($action);
        $controllerMock->expects($this->once())
            ->method('beforeExecute');
        $controllerMock->expects($this->once())
            ->method('afterExecute');

        $this->eventManagerMock->expects($this->at(0))
            ->method('dispatchEvent')
            ->with('app_dispatch_action', ['action' => $action, 'request' => $requestMock]);

        $this->eventManagerMock->expects($this->at(1))
            ->method('dispatchEvent')
            ->with(
                'app_controller_init',
                ['controller' => $controllerMock, 'action' => $action, 'request' => $requestMock]
            );

        $this->eventManagerMock->expects($this->at(2))
            ->method('dispatchEvent')
            ->with(
                'app_controller_initialized',
                ['controller' => $controllerMock, 'action' => $action, 'request' => $requestMock]
            );

        $this->eventManagerMock->expects($this->at(3))
            ->method('dispatchEvent')
            ->with(
                'app_controller_executed',
                ['controller' => $controllerMock, 'action' => $action, 'request' => $requestMock]
            );

        $this->app->dispatchAction($action);
        $this->assertEquals($action, $this->app->getCurrentAction());
    }

    public function testFlushAllCache()
    {
        $cacheConfig = new \SimpleXMLElement('<cache>
            <default>
                <type>file</type>
                <config>
                    <path>var/cache</path>
                </config>
            </default>
            <other>
                <memcache>
                    <server>
                        <host>127.0.0.1</host>
                        <port>11211</port>
                    </server>
                </memcache>
            </other>
        </cache>');
        $this->configMock->expects($this->once())
            ->method('getConfig')
            ->with('global/cache')
            ->will($this->returnValue($cacheConfig));
        $adapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($adapterPoolMock);

        $adapterMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            ['clear']
        );

        $adapterMock->expects($this->exactly(2))->method('clear');

        $adapterPoolMock->expects($this->at(0))->method('getAdapter')
            ->with('default')
            ->will($this->returnValue($adapterMock));

        $adapterPoolMock->expects($this->at(1))->method('getAdapter')
            ->with('other')
            ->will($this->returnValue($adapterMock));
        $this->app->flushAllCache();
    }
}
