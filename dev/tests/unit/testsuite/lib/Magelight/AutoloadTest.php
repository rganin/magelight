<?php

namespace Magelight;

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
