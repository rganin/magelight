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

/**
 * Logger class
 *
 * @method static \Magelight\Log getInstance()
 */
class Log
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Logfile path
     *
     * @var string
     */
    protected $_file = 'log.log';

    /**
     * Is logger initialized
     *
     * @var bool
     */
    protected $_initialized = false;

    /**
     * Log message
     *
     * @param string $logMessage
     * @return Log
     */
    public function add($logMessage)
    {
        if (!$this->_initialized) {
            $this->init();
        }
        $time = date('d-m-Y H:i:s', time());
        $message = "{$time} - {$logMessage}";
        $f = @fopen($this->_file, 'a+');
        if (!$f) {
            trigger_error("Log file or directory {$this->_file} is not writeable!", E_USER_WARNING);
        }
        flock($f, LOCK_EX);
        fwrite($f, $message . PHP_EOL);
        flock($f, LOCK_UN);
        fclose($f);
        return $this;
    }

    /**
     * Initialize application logger
     */
    protected function init()
    {
        $this->_file = (string)\Magelight\Config::getInstance()->getConfig('global/log/file', $this->_file);
        $this->_initialized = true;
        return $this;
    }
}
