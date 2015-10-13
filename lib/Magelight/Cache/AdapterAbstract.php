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
 * @method static \Magelight\Cache\AdapterAbstract forge($config)
 */
abstract class AdapterAbstract implements ICacheInterface
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Adapters pool
     *
     * @var array
     */
    protected static $_adapters = [];

    /**
     * Adapter configuration
     *
     * @var \SimpleXMLElement
     */
    protected $_config = null;

    /**
     * Cache key prefix
     *
     * @var string
     */
    protected $_cacheKeyPrefix = null;

    /**
     * Forgery constructor
     *
     * @param \SimpleXMLElement $config
     * @throws \Magelight\Exception
     */
    public function __forge($config)
    {
        $this->_config = $config;
        if (isset($config['cache_key_prefix'])) {
            $this->_cacheKeyPrefix = $config['cache_key_prefix'];
        } else {
            $this->_cacheKeyPrefix = (string) \Magelight\Config::getInstance()->getConfig('global/base_domain');
            if (!$this->_cacheKeyPrefix) {
                $this->_cacheKeyPrefix = md5(\Magelight::app()->getAppDir());
                if (!$this->_cacheKeyPrefix) {
                    throw new \Magelight\Exception('Cache key prefix not set, and base domain too. Cache conflicts can appear.');
                }
            }
        }
    }

    /**
     * Prepare cache key
     *
     * @param string $key
     * @return string
     */
    public function prepareKey($key)
    {
        return $this->_cacheKeyPrefix . $key;
    }

    /**
     * Get adapter class by type string
     *
     * @param string $type
     * @return string
     */
    public static function getAdapterClassByType($type)
    {
        return '\\Magelight\\Cache\\Adapters\\' . ucfirst(strtolower($type));
    }

    /**
     * Get adapter by index
     *
     * @param string $index
     * @return \Magelight\Cache\AdapterAbstract
     */
    protected  static function getAdapter($index)
    {
        $config = \Magelight\Config::getInstance()->getConfig('global/cache/' . $index);
        $type = self::getAdapterClassByType((bool)$config->disabled ? 'dummy' : $config->type);
        self::$_adapters[$index] = call_user_func_array([$type, 'forge'], [$config->config]);
        self::$_adapters[$index]->init();
        return self::$_adapters[$index];
    }

    /**
     * Get adapter instance by index
     *
     * @param string $index
     * @return \Magelight\Cache\AdapterAbstract
     */
    public static function getAdapterInstance($index)
    {
        if (isset(self::$_adapters[$index]) && self::$_adapters[$index] instanceof self) {
            return self::$_adapters[$index];
        }
        return self::getAdapter($index);
    }

    /**
     * Get all cache adapters described in config
     *
     * @return array
     */
    public static function getAllAdapters()
    {
        $adapters = [];
        $config = \Magelight\Config::getInstance()->getConfig('global/cache');
        foreach ($config->children() as $index => $cache) {
            $adapters[] = self::getAdapter($index);
        }
        return $adapters;
    }

    /**
     * Init adapter
     *
     * @return AdapterAbstract
     */
    abstract public function init();
}
