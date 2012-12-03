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

class Session
{
    use \Magelight\Forgery;

    /**
     * Getter
     *
     * @param string $name
     *
     * @return null
     */
    public function __get($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Get value from session
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    /**
     * Set session value
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Session
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * Unset session data
     *
     * @param string $name
     * @return Session
     */
    public function unsetData($name)
    {
        unset($_SESSION[$name]);
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
     * @param string $name
     *
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
