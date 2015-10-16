<?php

namespace Magelight;

class ObserverTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Observer
     */
    protected $observer;

    public function setUp()
    {
        $this->observer = $this->getMockForAbstractClass(Observer::class, [], '', false, false, true, []);
        $this->observer->__forge([
            'arg1' => 'arg1value',
            'arg2' => 'arg2value'
        ]);
    }

    public function testObserver()
    {
        $this->assertEquals('arg1value', $this->observer->arg1);
        $this->observer->arg3 = 'arg3value';
        $this->assertEquals('arg3value', $this->observer->arg3);
        $this->assertTrue(isset($this->observer->arg2));
        unset($this->observer->arg2);
        $this->assertFalse(isset($this->observer->arg2));
    }
}
