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
 * Logger class
 *
 * @method static \Magelight\Log getInstance()
 */
class Log
{
    /**
     * Default log file name
     */
    const DEFAUL_LOG_FILE = 'log.log';

    /**
     * Default date time format
     */
    const DATE_TIME_FORMAT = 'd-m-Y H:i:s';

    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Logfile path
     *
     * @var string
     */
    protected $file = self::DEFAUL_LOG_FILE;

    /**
     * Is logger initialized
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * Log message
     *
     * @param string $logMessage
     * @return Log
     */
    public function add($logMessage)
    {
        if (!$this->initialized) {
            $this->init();
        }
        $time = date(self::DATE_TIME_FORMAT, time());
        $message = "{$time} - {$logMessage}";
        $this->writeMessage($message);
        return $this;
    }

    /**
     * Write message to log file
     *
     * @param $message
     * @codeCoverageIgnore
     */
    protected function writeMessage($message)
    {
        $f = @fopen($this->file, 'a+');
        if (!$f) {
            trigger_error("Log file or directory {$this->file} is not writeable!", E_USER_WARNING);
        }
        flock($f, LOCK_EX);
        fwrite($f, $message . PHP_EOL);
        flock($f, LOCK_UN);
        fclose($f);
    }

    /**
     * Initialize application logger
     */
    protected function init()
    {
        $this->file = (string)\Magelight\Config::getInstance()->getConfig('global/log/file', self::DEFAUL_LOG_FILE);
        $this->initialized = true;
        return $this;
    }
}
