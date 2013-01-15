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
 * Cookie wrapper class
 *
 * @methos static \Magelight\Http\CookieObject forge($key = null, $value = null)
 *
 * @method \Magelight\Http\CookieObject setTtl(int $ttl)
 * @method \Magelight\Http\CookieObject setKey(string $key)
 * @method \Magelight\Http\CookieObject setValue(string $value)
 * @method \Magelight\Http\CookieObject setDomain(string $domain)
 * @method \Magelight\Http\CookieObject setPath(string $path)
 * @method \Magelight\Http\CookieObject setExpire(int $expire) - overrides TTL
 * @method \Magelight\Http\CookieObject setSecure(bool $secure)
 * @method \Magelight\Http\CookieObject setHttpOnly(bool $httpOnly)
 * @method int getTtl()
 * @method string getKey()
 * @method string|null getValue()
 * @method string getDomain()
 * @method string getPath()
 * @method string getExpire()
 * @method bool getSecure()
 * @method bool getHttpOnly()
 */
class CookieObject
{
    use \Magelight\Traits\TForgery;

    /**
     * Default cookie TTL
     */
    const DEFAULT_TTL = 86400;
    
    /**
     * TTL in seconds
     *
     * @var int
     */
    private $_ttl = null;
    
    /**
     * Cookie name
     *
     * @var string
     */
    private $_key = null;
    
    /**
     * Cookie value
     *
     * @var string
     */
    private $_value = null;
    
    /**
     * Cookie domain
     *
     * @var string
     */
    private $_domain = null;
    
    /**
     * Cookie path
     *
     * @var string
     */
    private $_path = null;
    
    /**
     * Cookie expiration timestamp
     *
     * @var int
     */
    private $_expire = null;
    
    /**
     * Is cookie secure flag
     *
     * @var bool
     */
    private $_secure = null;
    
    /**
     * is cookie for Http only flag
     *
     * @var bool
     */
    private $_httpOnly = null;
    
    /**
     * Forgery constructor
     * 
     * @param string $key
     * @param string $value
     */
    public function __forge($key = null, $value = null)
    {
        $this->_key = $key;
        $this->_value = $value;
        $this->_ttl = self::DEFAULT_TTL;
    }
    
    /**
     * Commit cookie (Set cookie to client)
     *
     * @return bool
     */
    public function commit()
    {
        return setcookie(
            $this->_key, 
            $this->_value, 
            $this->_expire, 
            $this->_path, 
            $this->_domain, 
            $this->_secure, 
            $this->_httpOnly
        );
    }
    
    /**
     * Setter
     * 
     * @param $name
     * @param $value
     *
     * @return CookieObject
     */
    public function __set($name, $value)
    {
        $name = '_' . lcFirst($name);
        $this->$name = $value;
        return $this;
    }
    
    /**
     * Getter
     * 
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $name = '_' . lcFirst($name);
        return $this->$name;
    }
}
