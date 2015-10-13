<?php

namespace Magelight;

use Magelight\Traits\TForgery;

class TestCase extends \PHPUnit_Framework_TestCase
{
    use TForgery;

    protected function tearDown()
    {
        \Magelight\Forgery\MockContainer::getInstance()->reset();
    }
}
