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
 * Class ProfilerTest
 * @package Magelight
 */
class ProfilerTest extends \Magelight\TestCase
{
    public function testGetInstance()
    {
        $profiler1 = \Magelight\Profiler::getInstance('profile1');
        $profiler1->startNewProfiling();
        $profiler2 = \Magelight\Profiler::getInstance('profile2');
        $this->assertNotEquals($profiler1, $profiler2);
    }

    public function testGetInstanceSame()
    {
        $profiler1 = \Magelight\Profiler::getInstance('profile11');
        $profiler1->startNewProfiling();
        $profiler2 = \Magelight\Profiler::getInstance('profile11');
        $this->assertEquals($profiler1, $profiler2);
    }

    public function testProfiler()
    {
        $profiler = \Magelight\Profiler::getInstance('test');
        $index = $profiler->startNewProfiling();
        usleep(300000);
        $profiler->finish($index);
        $data = $profiler->getProfile($index);
        $this->assertTrue($data['sec'] > floatval('0.2'));
    }
}
