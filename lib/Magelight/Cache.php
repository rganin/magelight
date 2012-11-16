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

class Cache implements \Magelight\Cache\CacheInterface
{
    use \Magelight\Forgery;

    /**
     * @var \Magelight\Cache\AdapterAbstract
     */
    protected $_adapter = null;

    public function init(array $config = [])
    {
        if (!isset($config['type'])) {
            throw new \Magelight\Exception('Cache type is not set in config node');
        }

        $adapterClass = \Magelight\Cache\AdapterAbstract::getAdapterClassByType($config['type']);
        $this->_adapter = new $adapterClass();
        $this->_adapter->init($config);
        return $this;
    }

    public function get($key, $default = null)
    {
        return $this->_adapter->get($key, $default);
    }

    public function set($key, $value, $ttl = 360)
    {
        return $this->_adapter->set($key, $value, $ttl);
    }

    public function del($key)
    {
        return $this->_adapter->del($key);
    }

    public function clear()
    {
        return $this->_adapter->clear();
    }

    public function increment($key, $incValue = 1)
    {
        return $this->_adapter->increment($key, $incValue);
    }

    public function decrement($key, $decValue = 1)
    {
        return $this->_adapter->decrement($key, $decValue);
    }
}