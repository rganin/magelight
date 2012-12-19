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

namespace Magelight\Cache\Adapters;

/**
 * @method static \Magelight\Cache\Adapters\Memcached forge($config)
 */
class Memcached extends \Magelight\Cache\AdapterAbstract
{

    /**
     * Memcache instance
     *
     * @var \Memcache
     */
    protected $memcached = null;

    /**
     * Initialize memcache
     *
     * @return \Magelight\Cache\Adapters\Memcached
     */
    public function init()
    {
        $this->memcached = new \Memcache();
        foreach ((array)$this->_config->xpath('servers/server') as $server) {
            $this->memcached->addserver(
                (string) $server->host,
                (int) $server->port
            );
        }
        return $this;
    }

    /**
     * Get cached value
     *
     * @param string $key
     * @param mixed $default
     * @return array|mixed|null|string
     */
    public function get($key, $default = null)
    {
        if ($data = $this->memcached->get($key)) {
            return $data;
        }
        return $default;
    }

    /**
     * Set cache value
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 360)
    {
        return $this->memcached->add($key, $value, 0, $ttl);
    }

    /**
     * Delete value from cache
     *
     * @param string $key
     * @return bool
     */
    public function del($key)
    {
        return (bool)$this->memcached->delete($key);
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function clear()
    {
        return (bool)$this->memcached->flush();
    }

    /**
     * Increment cache value
     *
     * @param string $key
     * @param int $incValue
     * @return int|null
     */
    public function increment($key, $incValue = 1)
    {
        return $this->memcached->increment($key, $incValue);
    }

    /**
     * Decrement cached value
     *
     * @param string $key
     * @param int $decValue
     * @return int|null
     */
    public function decrement($key, $decValue = 1)
    {
        return $this->memcached->decrement($key, $decValue);
    }
}
