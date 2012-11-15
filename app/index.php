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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
$t = microtime();
require '../core.php';
//
//$xml1 = simplexml_load_file('etc/test/config1.xml');
//$xml2 = simplexml_load_file('etc/test/config2.xml');
//$xml3 = simplexml_load_file('etc/test/config3.xml');
//
//
//\Magelight\Components\Loaders\Config::mergeConfig($xml1, $xml2);
//\Magelight\Components\Loaders\Config::mergeConfig($xml1, $xml3);

//\Magelight::dump(htmlspecialchars($xml1->asXML()));
//die();
Magelight::app()
    ->setAppDir(dirname(__FILE__))
    ->setDeveloperMode(true)
    ->init()
    ->run();
$s = microtime() - $t;
echo $s;
