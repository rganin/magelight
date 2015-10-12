<?php

namespace Magelight\Observer;

use Magelight\Traits\TForgery;

/**
 * Class Factory
 * @package Magelight\Observer
 *
 * @method static \Magelight\Observer\Factory getInstance()
 */
class Factory
{
    use TForgery;

    public function create($className, $arguments = [])
    {
        return call_user_func_array([$className, 'forge'], [$arguments]);
    }
}