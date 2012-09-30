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

namespace Magelight\Http;

class Session extends \Magelight\Forgery\SingletonForgery
{
    /**
     * Getter
     *
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Setter
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Get value from session
     *
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    /**
     * Set session value
     *
     * @param $name
     * @param $value
     * @return Session
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * Start session
     *
     * @return Session
     */
    public function start()
    {
        session_start();
        return $this;
    }

    /**
     * Commit session
     *
     * @return Session
     */
    public function commit()
    {
        session_commit();
        return $this;
    }

    /**
     * Close session
     *
     * @return Session
     */
    public function close()
    {
        session_write_close();
        return $this;
    }

    /**
     * Set session name
     *
     * @param $name
     * @return Session
     */
    public function setSessionName($name)
    {
        session_name($name);
        return $this;
    }

    /**
     * Get session identifier
     *
     * @return string
     */
    public function getSessionId()
    {
        return session_id();
    }
}