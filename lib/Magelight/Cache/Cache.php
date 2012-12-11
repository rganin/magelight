<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 11.12.12
 * Time: 19:39
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Cache;

/**
 * Cacher trait
 *
 * @property string $_cacherTraitCacheIndex
 * @property string|array $_cacherTraitCacheKey
 * @property int $_cacherTraitCacheTtl
 */
trait Cache
{
    protected $_cacherTraitCacheIndex = \Magelight\App::DEFAULT_INDEX;
    protected $_cacherTraitCacheKey = false;
    protected $_cacherTraitCacheTtl = 3600;
    /**
     * Build cache key with array of params
     *
     * @param array $keyParams
     * @return string
     */
    public function buildCacheKey($keyParams)
    {
        if (!is_array($keyParams)) {
            $keyParams = [$keyParams];
        }
        return md5(serialize($keyParams));
    }

    /**
     * Get cache adapter instance
     *
     * @return AdapterAbstract
     */
    public function cache()
    {
        return AdapterAbstract::getAdapterInstance($this->getCacheIndex());
    }

    /**
     * Get item from cache
     *
     * @param null $default
     * @return mixed
     */
    public function getFromCache($default = null)
    {
        if (!$this->getCacheKey()) {
            return $default;
        }
        return $this->cache()->get($this->buildCacheKey($this->getCacheKey()), $default);
    }

    /**
     * Set item to cache
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function setToCache($value)
    {
        if (!$this->getCacheKey()) {
            return false;
        }
        return $this->cache()->set($this->buildCacheKey($this->getCacheKey()), $value, $this->getCacheTtl());
    }

    /**
     * Enable caching
     *
     * @param $cacheKey
     * @param int $ttl
     * @param string $cacheIndex
     * @return Cache
     */
    public function useCache($cacheKey, $ttl = 3600, $cacheIndex = \Magelight\App::DEFAULT_INDEX)
    {
        $this->_cacherTraitCacheKey = $cacheKey;
        $this->_cacherTraitCacheIndex = $cacheIndex;
        $this->_cacherTraitCacheTtl = $ttl;
        return $this;
    }

    /**
     * Get cache key
     *
     * @return array|bool|string
     */
    public function getCacheKey()
    {
        return isset($this->_cacherTraitCacheKey) ? $this->_cacherTraitCacheKey : false;
    }

    /**
     * Get cacher index
     *
     * @return string
     */
    public function getCacheIndex()
    {
        return isset($this->_cacherTraitCacheIndex) ? $this->_cacherTraitCacheIndex : \Magelight\App::DEFAULT_INDEX;
    }

    /**
     * Get cache TTL
     *
     * @return int
     */
    public function getCacheTtl()
    {
        return isset($this->_cacherTraitCacheTtl) ? $this->_cacherTraitCacheTtl : 3600;
    }

    /**
     * Is caching enabled
     *
     * @return array|bool|string
     */
    public function cacheEnabled()
    {
        return $this->getCacheKey();
    }

    /**
     * Proxy cache settings to object
     *
     * @param object $object
     * @return Cache
     * @throws \Magelight\Exception
     */
    public function proxyCacheTo($object)
    {
        // todo: refine this crap when https://github.com/php/php-src/pull/23 will be merged to master and released
        if (!method_exists($object, 'buildCacheKey')) {
            throw new \Magelight\Exception(
                "Object passed to " . __METHOD__ . " must use " . __TRAIT__ . " trait!"
            );
        }
        $object->useCache($this->getCacheKey(), $this->getCacheTtl(), $this->getCacheIndex());
        return $this;
    }
}
