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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight;

class Log
{
    protected static $_file = 'log.log';

    protected static $_initialized = false;

    /**
     * Add message to application log
     * 
     * @static
     * @param $logMessage
     */
    public static function add($logMessage)
    {
        if (!self::$_initialized) {
            self::init();
        }
        $time = date('d-m-Y H:i:s', time());
        $message = "{$time} - {$logMessage}";
        $f = @fopen(self::$_file, 'a+');
        if (!$f) {
            file_put_contents(\Magelight::app()->getAppDir() . DS . self::$_file, '', FILE_APPEND);
        }
        flock($f, LOCK_EX);
        fwrite($f, $message . PHP_EOL);
        flock($f, LOCK_UN);
        fclose($f);
    }

    protected static function init()
    {
        self::$_file = (string)\Magelight::app()->config()->getConfig('global/log/file', self::$_file);
        self::$_initialized = true;
    }
}
