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

namespace Magelight\Http;

/**
 * Http server var wrapper + helper
 *
 * @method static \Magelight\Http\Server getInstance()
 */
class Server
{
    use \Magelight\Traits\TForgery;

    /**
     * Overriding forgery
     *
     * @return null
     */
    public static function forge()
    {
        return null;
    }

    /**
     * Get requested domain
     * 
     * @return string
     * @throws \Magelight\Exception
     */
    public function getCurrentDomain()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        throw new \Magelight\Exception('Global server variable HTTP_HOST is required but missing.');
    }

    /**
     * Get remote connection IP
     *
     * @return string
     */
    public function getRemoteIp()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        trigger_error('Remote IP is unknown');
        return '';
    }

    /**
     * Get referer
     *
     * @param string $default
     *
     * @return string
     */
    public function getHttpReferer($default = '')
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
        return $default;
    }

    /**
     * Send header to client
     *
     * @param string $header
     */
    public function sendHeader($header)
    {
        header($header);
    }
}
