<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.02.13
 * Time: 9:20
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

abstract class Aspect
{
    public function __invoke(\ReflectionMethod $method)
    {

    }
}