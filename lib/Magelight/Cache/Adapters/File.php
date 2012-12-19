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
 * @method static \Magelight\Cache\Adapters\File forge($config)
 */
class File extends \Magelight\Cache\AdapterAbstract
{
    /**
     * File cache directory
     *
     * @var string
     */
    protected $_path = '/var/tmp';

    /**
     * Initialize cache
     *
     * @return \Magelight\Cache\AdapterAbstract|File
     */
    public function init()
    {
        $this->_path = isset($this->_config->path)
            ? \Magelight::app()->getAppDir() . DS . trim((string)$this->_config->path, '\\/')
            : $this->_path;
        return $this;
    }

    /**
     * Build file path by key value
     *
     * @param string $key
     *
     * @return string
     */
    public function getFilepath($key)
    {
        return $this->_path . DS . $key;
    }

    /**
     * Get cached value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $data = @file_get_contents($this->getFilepath($key));
        if ($data === false) {
            return $default;
        }
        $data = unserialize($data);
        if (!empty($data['ttl']) && $data['ttl'] < time()) {
            return $default;
        }
        return array_key_exists('value' , $data) ? $data['value'] : $default;
    }

    /**
     * Set value to cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 360)
    {
        $data = serialize(['ttl' => time() + $ttl, 'value' => $value]);
        return (bool) @file_put_contents($this->getFilepath($key) , $data);
    }

    /**
     * Delete value from cache
     *
     * @param string $key
     * @return bool
     */
    public function del($key)
    {
        $result = true;
        foreach (glob(trim($this->_path, '\\/') . DS) as $file) {
            $result &= (bool)@unlink($file);
        }
        return $result;
    }

    /**
     * Clear file cache
     *
     * @return bool|mixed
     */
    public function clear()
    {
        return true;
    }

    /**
     * Increment cache value
     *
     * @param string $key
     * @param int $incValue
     * @return bool
     */
    public function increment($key, $incValue = 1)
    {
        $file = @fopen($this->getFilepath($key), 'r');
        @flock($file, LOCK_EX);
        $value = $this->get($file, 0) + $incValue;
        $result = $this->set($key, $value, 0);
        @flock($file, LOCK_UN);
        return $result;
    }

    /**
     * Decrement cached value
     *
     * @param string $key
     * @param int $decValue
     * @return bool
     */
    public function decrement($key, $decValue = 1)
    {
        $file = @fopen($this->getFilepath($key), 'r');
        @flock($file, LOCK_EX);
        $value = $this->get($file, 0) - $decValue;
        $result = $this->set($key, $value, 0);
        @flock($file, LOCK_UN);
        return $result;
    }
}
