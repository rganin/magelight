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
 * Cookies array wrapper class
 *
 * @method static \Magelight\Http\Cookies forge()
 */
class Cookies
{
    use \Magelight\Traits\TForgery;

    /**
     * Cookies
     * 
     * @var array
     */
    private $_cookies = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_cookies = &$_COOKIE;
    }

    /**
     * Get cookie by name
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($key, $default = null)
    {
        if ($this->cookieExists($key)) {
            return $this->_cookies[$key];
        }
        return $default;
    }
    
    /**
     * Check if cookie exists
     * 
     * @param string $key
     * @return bool
     */
    public function cookieExists($key)
    {
        return isset($this->_cookies[$key]);
    }
    
    /**
     * Get cookie by name as object
     * 
     * @param string $key
     * @return CookieObject|null
     */
    public function getCookieObject($key)
    {
        if ($this->cookieExists($key)) {
            return CookieObject::forge($key, $this->_cookies[$key]);
        }
        return null;
    }
}
