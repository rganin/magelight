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

namespace Magelight\Event;

/**
 * Class ManagerTest
 * @package Magelight\Event
 */
class ManagerTest extends \Magelight\TestCase
{

    /**
     * @var \Magelight\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magelight\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * Set up before test
     *
     * @throws \Magelight\Exception
     */
    public function setUp()
    {
        $this->eventManager = \Magelight\Event\Manager::forge();
        $this->configMock = $this->getMockBuilder(\Magelight\Config::class)->disableOriginalConstructor()->getMock();
        \Magelight\Config::forgeMock($this->configMock);
    }

    /**
     * @test
     */
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
     * @test
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
