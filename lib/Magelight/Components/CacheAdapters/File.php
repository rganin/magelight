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
 * @version   $$version_placeholder_notice$$
 * @author     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components\CacheAdapters;

class File implements AdapterInterface
{
    /**
     * Cache directory in app path
     */
    const CACHE_DIR = 'var/cache';

    /**
     * Initialize adapter
     * 
     * @param int $conpression
     *
     * @return bool
     */
    public function init($conpression = \Magelight\Components\Cache::COMPRESSION_OFF)
    {
        return true;
    }

    /**
     * Get adapter cache directory
     * 
     * @return string
     */
    protected function getCacheDir()
    {
        return \Magelight::app()->getAppDir() . DS . self::CACHE_DIR;
    }

    /**
     * Hash key
     * 
     * @param $key
     *
     * @return string
     */
    protected function hashKey($key)
    {
        return md5($key);
    }

    /**
     * Form path by key
     * 
     * @param $key
     *
     * @return string
     */
    protected function pathByKey($key)
    {
        return $this->getCacheDir() . DS . $key;
    }

    /**
     * Get value
     * 
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $value = @file_get_contents($this->pathByKey($key));
        if ($value) {
            return unserialize($value);
        }
        return $default;
    }

    /**
     * Set value
     * 
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return int
     */
    public function set($key, $value, $ttl = 360)
    {
        return @file_put_contents($this->pathByKey($key), serialize($value));
    }

    /**
     * Delete value
     * 
     * @param $key
     */
    public function del($key)
    {
        unlink($this->pathByKey($key));
    }

    /**
     * Clear cache (not working in file cache)
     * 
     * @return bool
     */
    public function clear()
    {
        return true;
    }

    /**
     * Increment value
     * 
     * @param     $key
     * @param int $incValue
     *
     * @return int
     */
    public function increment($key, $incValue = 1)
    {
        $value = (int) $this->get($key, 0);
        return $this->set($key, $value + $incValue);
    }

    /**
     * Decrement value
     * 
     * @param     $key
     * @param int $decValue
     *
     * @return int
     */
    public function decrement($key, $decValue = 1)
    {
        $value = (int) $this->get($key, 0);
        return $this->set($key, $value - $decValue);
    }
}
