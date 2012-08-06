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
 * @version   $$version_placeholder_notice$$
 * @uthor     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Cache;

class Cache extends \Bike\Prototypes\Singleton
{
    /**
     * Default cache namespace
     */
    const DEFAULT_NAMESPACE = 'default';

    /**
     * Cache types
     */
    const TYPE_MEMCACHED = 'Memcached';
    const TYPE_APC = 'Apc';
    const TYPE_FILE = 'File';
    const TYPE_DUMMY = 'Dummy';

    /**
     * Cache compressions
     */
    const COMPRESSION_OFF = 0;

    /**
     * Cache adapter
     * 
     * @var \Bike\Cache\Adapters\AdapterInterface
     */
    protected $_adapter = null;

    /**
     * Cache namespace
     * 
     * @var string
     */
    protected $_namespace = self::DEFAULT_NAMESPACE;

    /**
     * Initialize cache instance
     * 
     * @param string $type
     * @param string $namespace
     * @param int    $compression
     *
     * @return Cache
     * @throws \Bike\Exception
     */
    public function init($type = self::TYPE_FILE, 
                         $namespace = self::DEFAULT_NAMESPACE, 
                         $compression = self::COMPRESSION_OFF
    )
    {
        try {
            $adapterName = '\\Bike\\Cache\\Adapters\\' . ucfirst($type);
            $adapter = new $adapterName();
            /* @var \Bike\Cache\Adapters\AdapterInterface $adapter*/
            $adapter->init($compression);
            $this->_adapter = $adapter;
            $this->_namespace = $namespace;
        } catch (\Exception $e) {
            throw new \Bike\Exception($e->getMessage());
        }
        return $this;
    }

    /**
     * Get namespaced key
     * 
     * @param $key
     *
     * @return string
     */
    protected function getNsKey($key)
    {
        return $this->_namespace . $key;
    }


    /**
     * Get value by key
     * 
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->_adapter->get($this->getNsKey($key), $default);
    }

    /**
     * Set value by key
     * 
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return mixed
     */
    public function set($key, $value, $ttl = 360)
    {
        return $this->_adapter->set($this->getNsKey($key), $value, $ttl);
    }

    /**
     * Delete value by key
     * 
     * @param $key
     *
     * @return mixed
     */
    public function del($key)
    {
        return $this->_adapter->del($this->getNsKey($key));
    }

    /**
     * Clear cache
     * 
     * @return mixed
     */
    public function clear()
    {
        return $this->_adapter->clear();
    }

    /**
     * Increment value
     * 
     * @param     $key
     * @param int $incValue
     *
     * @return mixed
     */
    public function increment($key, $incValue = 1)
    {
        return $this->_adapter->increment($this->getNsKey($key), $incValue);
    }

    /**
     * Decrement value
     * 
     * @param     $key
     * @param int $decValue
     *
     * @return mixed
     */
    public function decrement($key, $decValue = 1)
    {
        return $this->_adapter->increment($this->getNsKey($key), $decValue);
    }
}