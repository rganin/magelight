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

namespace Magelight;

/**
 * Class ObserverTest
 * @package Magelight
 */
class ObserverTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Observer
     */
    protected $observer;

    /**
     * {@inheritdoc}
     */
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
