<?php
/**
 * $$name_placeholder_notice$$
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
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike;

class Log
{
    /**
     * Add message to application log
     * 
     * @static
     * @param $logMessage
     * @param string $logFile
     */
    public static function add($logMessage, $logFile = 'error.log')
    {
        $time = date('d-m-Y H:i:s', time());
        $message = "{$time} - {$logMessage}";
        $f = fopen($logFile, 'a+');
        flock($f, LOCK_EX);
        fwrite($f, $message . PHP_EOL);
        flock($f, LOCK_UN);
        fclose($f);
    }
}
