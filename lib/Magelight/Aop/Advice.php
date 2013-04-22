<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 04.04.13
 * Time: 15:24
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Aop;

class Advice
{
    protected $_callback;

    public function __construct()
    {

    }

    public function getCallback()
    {
        return function () use ($this) {
            $this->execute();
        };
    }

    public function execute()
    {

    }
}