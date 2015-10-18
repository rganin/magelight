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
 * Class AutoloadTest
 * @package Magelight
 */
class AutoloadTest extends \Magelight\TestCase
{
    public function testAutoload()
    {
        set_include_path(get_include_path() . PS . __DIR__ . DS . '_fixtures' . DS . 'test_include_path');
        $autoload = new \Magelight\Autoload();

        $autoload->autoload('AutoloadTestClass');

        $testClass = new \AutoloadTestClass();
        $this->assertEquals('bar', $testClass->foo());
    }
}
