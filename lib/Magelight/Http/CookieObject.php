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
/**
 * @method CookieObject setTtl(int $ttl)
 * @method CookieObject setKey(string $key)
 * @method CookieObject setValue(string $value)
 * @method CookieObject setDomain(string $domain)
 * @method CookieObject setPath(string $path)
 * @method CookieObject setExpire(int $expire) - overrides TTL
 * @method CookieObject setSecure(bool $secure)
 * @method CookieObject setHttpOnly(bool $httpOnly)
 * @method getTtl
 * @method getKey
 * @method getValue
 * @method getDomain
 * @method getPath
 * @method getExpire
 * @method getSecure
 * @method getHttpOnly
 */
class CookieObject
{
    /**
     * Default cookie TTL
     */
    const DEFAULT_TTL = 86400;
    
    /**
     * TTL in seconds
     * @var int
     */
    private $_ttl = null;
    
    /**
     * Cookie name
     * @var string
     */
    private $_key = null;
    
    /**
     * Cookie value
     * @var string
     */
    private $_value = null;
    
    /**
     * Cookie domain
     * @var string
     */
    private $_domain = null;
    
    /**
     * Cookie path
     * @var string
     */
    private $_path = null;
    
    /**
     * Cookie expiration timestamp
     * @var int
     */
    private $_expire = null;
    
    /**
     * Is cookie secure flag 
     * @var bool
     */
    private $_secure = null;
    
    /**
     * is cookie for Http only flag
     * @var bool
     */
    private $_httpOnly = null;
    
    /**
     * Constructor
     * 
     * @param string $key
     * @param string $value
     */
    public function __construct($key = null, $value = null)
    {
        $this->_key = $key;
        $this->_value = $value;
        $this->_ttl = self::DEFAULT_TTL;
    }
    
    /**
     * Commit cookie (Set cookie to client)
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
     * @return mixed
     */
    public function __get($name)
    {
        $name = '_' . lcFirst($name);
        return $this->$name;
    }
}
