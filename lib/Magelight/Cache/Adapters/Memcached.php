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
     * @var \Memcache
     */
    protected $memcached = null;

    public function init()
    {
        $this->memcached = new \Memcache();
        foreach ((array)$this->_config->xpath('servers/server') as $server) {
            $this->memcached->addserver(
                (string) $server->host,
                (int) $server->port
            );
        }
    }

    public function get($key, $default = null)
    {
        if ($data = $this->memcached->get($key)) {
            return $data;
        }
        return $default;
    }

    public function set($key, $value, $ttl = 360)
    {
        return $this->memcached->add($key, $value, 0, $ttl);
    }

    public function del($key)
    {
        $this->memcached->delete($key);
    }

    public function clear()
    {
        $this->memcached->flush();
    }

    public function increment($key, $incValue = 1)
    {
        return $this->memcached->increment($key, $incValue);
    }

    public function decrement($key, $decValue = 1)
    {
        return $this->memcached->decrement($key, $decValue);
    }
}
