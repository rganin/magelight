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

class AppTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magelight\Components\Config|\PHPUnit_Framework_MockObject_MockObject
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
     * Set up before test
     */
    public function setUp()
    {
        $this->app = new App();

        $this->modulesMock = $this->getMock(\Magelight\Components\Modules::class, [], [], '', false);
        $this->configMock = $this->getMock(\Magelight\Components\Config::class, [], [], '', false);
        $this->routerMock = $this->getMock(\Magelight\Components\Router::class, [], [], '', false);
        $this->sessionMock = $this->getMock(\Magelight\Http\Session::class, [], [], '', false);
        $this->translatorMock = $this->getMock(\Magelight\I18n\Translator::class, [], [], '', false);

        \Magelight\Components\Modules::forgeMock($this->modulesMock);
        \Magelight\Components\Config::forgeMock($this->configMock);
        \Magelight\Components\Router::forgeMock($this->routerMock);
        \Magelight\Http\Session::forgeMock($this->sessionMock);
        \Magelight\I18n\Translator::forgeMock($this->translatorMock);
    }

    public function testGeneric()
    {
        $this->assertTrue($this->app->setAppDir('') instanceof App);
        $this->assertTrue($this->app->setDeveloperMode(true) instanceof App);
        $this->assertTrue($this->app->setSessionCookieName('SESSION') instanceof App);
        $this->assertTrue($this->app->setRegistryObject('dfs', $this) instanceof App);
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

    public function testFireEvent()
    {
        $eventName = 'test_event';
        $observerClass1 = '\Magelight\Observer::execute';
        $observers = [$observerClass1];
        $this->configMock->expects($this->once())
            ->method('getConfigSet')
            ->with('global/events/' . $eventName . '/observer')
            ->will($this->returnValue($observers));
        $observerMock = $this->getMockForAbstractClass('\Magelight\Observer', [], '', false, false, true, ['execute']);
        \Magelight\Observer::forgeMock($observerMock);
        $observerMock->expects($this->once())->method('execute');
        $this->app->fireEvent('test_event');
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Observer '\Magelight\Observer' method 'unexistentMethod' does not exist or is not callable!
     */
    public function testFireEventException()
    {
        $eventName = 'test_event';
        $observerClass1 = '\Magelight\Observer::unexistentMethod';
        $observers = [$observerClass1];
        $this->configMock->expects($this->once())
            ->method('getConfigSet')
            ->with('global/events/' . $eventName . '/observer')
            ->will($this->returnValue($observers));
        $observerMock = $this->getMockForAbstractClass('\Magelight\Observer', [], '', false, false, true, ['execute']);
        \Magelight\Observer::forgeMock($observerMock);
        $this->app->fireEvent('test_event');
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
        $mysqlDbAdapterMock->expects($this->once())->method('init')->with((array)$mysqlConfig);
        \Magelight\Db\Mysql\Adapter::forgeMock($mysqlDbAdapterMock);
        $this->configMock->expects($this->once())->method('getConfig')->with('/global/db/' . $index, null)->will($this->returnValue($mysqlConfig));
        $this->assertInstanceOf(\Magelight\Db\Mysql\Adapter::class, $this->app->db());
    }
}
