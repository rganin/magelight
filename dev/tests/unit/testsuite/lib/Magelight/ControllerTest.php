<?php

namespace Magelight;

class ControllerTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Http\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magelight\Http\Response|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magelight\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appMock;

    /**
     * @var \Magelight\Http\Server|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serverMock;

    /**
     * @var \Magelight\Controller
     */
    protected $controller;

    public function setUp()
    {
        $this->requestMock = $this->getMock(\Magelight\Http\Request::class, [], [], '', false);
        \Magelight\Http\Request::forgeMock($this->requestMock);

        $this->responseMock = $this->getMock(\Magelight\Http\Response::class, [], [], '', false);
        \Magelight\Http\Response::forgeMock($this->responseMock);

        $this->appMock = $this->getMock(\Magelight\App::class, [], [], '', false);
        \Magelight\App::forgeMock($this->appMock);

        $this->serverMock = $this->getMock(\Magelight\Http\Server::class, [], [], '', false);
        \Magelight\Http\Server::forgeMock($this->serverMock);

        $this->controller = \Magelight\Controller::forge();
    }

    public function testInit()
    {
        $this->controller->init();
    }

    public function testSetViewBlock()
    {
        $blockMock = $this->getMock(\Magelight\Block::class, [], [], '', false);
        $this->controller->setView($blockMock);
        $this->assertEquals($blockMock, $this->controller->view());
    }

    public function testSetViewString()
    {
        $blockMock = $this->getMock(\Magelight\Block::class, [], [], '', false);
        \Magelight\Block::forgeMock($blockMock);
        $this->controller->setView(\Magelight\Block::class);
        $this->assertEquals($blockMock, $this->controller->view());
    }

    public function testSetViewEmpty()
    {
        $this->assertEquals(null, $this->controller->view());
    }

    public function testApp()
    {
        $this->controller->init();
        $this->assertEquals($this->appMock, $this->controller->app());
    }

    public function testRequest()
    {
        $this->controller->init();
        $this->assertEquals($this->requestMock, $this->controller->request());
    }

    public function testResponse()
    {
        $this->controller->init();
        $this->assertEquals($this->responseMock, $this->controller->response());
    }

    public function testServer()
    {
        $this->controller->init();
        $this->assertEquals($this->serverMock, $this->controller->server());
    }

    public function testRenderView()
    {
        $blockMock = $this->getMock(\Magelight\Block::class, [], [], '', false);
        \Magelight\Block::forgeMock($blockMock);
        $this->controller->setView(\Magelight\Block::class);

        $this->controller->init();
        $blockMock->expects($this->once())->method('toHtml')->will($this->returnValue('HTML OUTPUT'));
        $this->responseMock->expects($this->once())->method('setContent')->will($this->returnSelf());
        $this->responseMock->expects($this->once())->method('send');

        $this->assertEquals($this->controller, $this->controller->renderView());
    }

    public function testForward()
    {
        /** @var \Magelight\Controller|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockForAbstractClass(
            \Magelight\Controller::class,
            [],
            '',
            false,
            false,
            true,
            ['forwardAction']
        );
        $controller->expects($this->once())->method('forwardAction');
        $controller->forward('forward');
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Forwarding to undefined controller action unexistentAction in
     */
    public function testForwardNoAction()
    {
        /** @var \Magelight\Controller|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockForAbstractClass(
            \Magelight\Controller::class,
            [],
            '',
            false,
            false,
            true,
            ['forwardAction']
        );
        $controller->forward('unexistent');
    }

    public function testUrl()
    {
        $urlHelperMock = $this->getMock(\Magelight\Helpers\UrlHelper::class, [], [], '', false);
        $urlHelperMock->expects($this->once())
            ->method('getUrl')
            ->with('match', ['param' => 'value'], 'http')
        ->will($this->returnValue('http://url/match?param=value'));
        \Magelight\Helpers\UrlHelper::forgeMock($urlHelperMock);
        $this->assertEquals(
            'http://url/match?param=value',
            $this->controller->url('match', ['param' => 'value'], 'http')
        );
    }

    public function testRedirect()
    {
        $url = 'http://redirect/';
        $this->serverMock->expects($this->once())->method('sendHeader')->with("Location: " . $url);
        $this->appMock->expects($this->once())->method('shutdown');
        $this->controller->redirect($url);
    }

    public function testRedirectInternal()
    {
        $url = 'http://url/match?param=value';
        $urlHelperMock = $this->getMock(\Magelight\Helpers\UrlHelper::class, [], [], '', false);
        $urlHelperMock->expects($this->once())
            ->method('getUrl')
            ->with('match', ['param' => 'value'], 'http')
            ->will($this->returnValue('http://url/match?param=value'));
        \Magelight\Helpers\UrlHelper::forgeMock($urlHelperMock);
        $this->serverMock->expects($this->once())->method('sendHeader')->with("Location: " . $url);
        $this->appMock->expects($this->once())->method('shutdown');
        $this->controller->redirectInternal('match', ['param' => 'value']);
    }

    public function testSession()
    {
        $sessionMock = $this->getMock(\Magelight\Http\Session::class, [], [], '', false);
        \Magelight\Http\Session::forgeMock($sessionMock);
        $this->assertEquals($sessionMock, $this->controller->session());
    }

    public function testForwardController()
    {
        $this->controller->init();
        $this->appMock->expects($this->once())->method('getCurrentAction')->will($this->returnValue([
            [
                'module' => 'Module1',
                'controller' => 'index',
                'action' => 'index'
            ]
        ]));
        $this->appMock->expects($this->once())->method('dispatchAction')->with(
            [
                'module' => 'Module1',
                'controller' => 'index',
                'action' => 'forwarded'
            ]
        );
        $this->controller->forwardController('index', 'forwarded');
    }

    public function testToken()
    {
        $this->controller->init();
        $sessionMock = $this->getMock(\Magelight\Http\Session::class, [], [], '', false);
        \Magelight\Http\Session::forgeMock($sessionMock);
        $sessionMock->expects($this->once())->method('set')->with(
            \Magelight\Controller::DEFAULT_TOKEN_SESSION_INDEX,
            $this->matchesRegularExpression('/[\d]+/i')
        );
        $this->controller->generateToken();

        $sessionMock->expects($this->any())
            ->method('get')
            ->with(\Magelight\Controller::DEFAULT_TOKEN_SESSION_INDEX, null)
            ->will($this->returnValue(123456789));

        $this->assertTrue($this->controller->checkToken(123456789));
        $this->assertFalse($this->controller->checkToken(987654321));
    }

    public function testSilentAction()
    {
        /** @var \Magelight\Controller|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockForAbstractClass(
            \Magelight\Controller::class,
            [],
            '',
            false,
            false,
            true,
            ['silentAction']
        );
        $controller->expects($this->once())->method('silentAction');
        $controller->silent('silentAction');
    }

    /**
     * @param $cacheReturnValue
     * @param $expectedResult
     *
     * @dataProvider testLockActionDataProvider
     */
    public function testLockAction($cacheReturnValue, $expectedResult)
    {
        $ttl = 33;
        $cacheMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            ['setNx']
        );

        $adapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($adapterPoolMock);

        $adapterPoolMock->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($cacheMock));

        $cacheMock->expects($this->any())
            ->method('setNx')
            ->with(md5(serialize([]) . '_lock'), 1, $ttl)
            ->will($this->returnValue($cacheReturnValue));

        $this->controller->init();
        $this->assertEquals($expectedResult, $this->controller->lockCurrentAction($ttl));
    }

    public function testLockActionDataProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * @param $cacheReturnValue
     * @param $expectedResult
     *
     * @dataProvider testUnlockActionDataProvider
     */
    public function testUnlockAction($cacheReturnValue, $expectedResult)
    {
        $cacheMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            ['del']
        );

        $adapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($adapterPoolMock);

        $adapterPoolMock->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($cacheMock));

        $cacheMock->expects($this->any())
            ->method('del')
            ->with(md5(serialize([]) . '_lock'))
            ->will($this->returnValue($cacheReturnValue));

        $this->controller->init();
        $this->assertEquals($expectedResult, $this->controller->unlockCurrentAction());
    }

    public function testUnlockActionDataProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
