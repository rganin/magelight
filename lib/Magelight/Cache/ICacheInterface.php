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

namespace Magelight\Cache;

/**
 * Cache adapter interface
 */
interface ICacheInterface
{
    /**
     * Get cached value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set value to cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return mixed
     */
    public function set($key, $value, $ttl = 360);

    /**
     * Delete value from cache by key
     *
     * @param string $key
     * @return mixed
     */
    public function del($key);

    /**
     * Clear cache
     *
     * @return mixed
     */
    public function clear();

    /**
     * Increment cache value
     *
     * @param string $key
     * @param int $incValue
     * @param int|null $initialValue
     * @param int $ttl
     * @return int|bool
     */
    public function increment($key, $incValue = 1, $initialValue = 0, $ttl = 360);

    /**
     * Decrement cache value
     *
     * @param string $key
     * @param int $decValue
     * @param int|null $initialValue
     * @param int $ttl
     * @return mixed
     */
    public function decrement($key, $decValue = 1, $initialValue = 0, $ttl = 360);

    /**
     * Set value if not exists. Returns bool TRUE if value didn`t exist and successfully set.
     * False - if value alreadye xists
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return mixed
     */
    public function setNx($key, $value, $ttl = 360);
}
