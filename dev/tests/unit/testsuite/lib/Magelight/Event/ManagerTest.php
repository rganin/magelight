<?php

namespace Magelight\Event;

class EventTest extends \Magelight\TestCase
{

    /**
     * @var \Magelight\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magelight\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    public function setUp()
    {
        $this->eventManager = \Magelight\Event\Manager::forge();
        $this->configMock = $this->getMock(\Magelight\Config::class, [], [], 'ConfigMock', false);
        \Magelight\Config::forgeMock($this->configMock);
    }

    public function testDispatchEvent()
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
        $this->eventManager->dispatchEvent('test_event');
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Observer '\Magelight\Observer' method 'unexistentMethod' does not exist or is not callable!
     */
    public function testDispatchEventException()
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
        $this->eventManager->dispatchEvent('test_event');
    }
}
