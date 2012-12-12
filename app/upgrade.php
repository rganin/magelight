<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 20:52
 * To change this template use File | Settings | File Templates.
 */
require '../core.php';

Magelight::app()
    ->setAppDir(dirname(__FILE__))
    ->setDeveloperMode(true)
    ->init()
    ->flushAllCache()
    ->upgrade();