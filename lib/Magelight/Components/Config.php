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

namespace Magelight\Components;

/**
 * Config wrapper
 */
class Config
{
    use \Magelight\Cache\Cache;

    /**
     * Config path prefix
     */
    const CONFIG_PATH_PREFIX = '//config/';

    /**
     * Condif values types
     */
    const TYPE_STRING   = 'string';
    const TYPE_ARRAY    = 'array';
    const TYPE_INT      = 'int';
    const TYPE_FLOAT    = 'float';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_XML_NODE = 'xml_node';

    /**
     * @var \SimpleXMLElement
     */
    protected $_config = null;

    /**
     * Constructor
     * 
     * @param \Magelight\App $app
     */
    public function __construct(\Magelight\App $app)
    {
        /* Loading APP config */
        $loader = new \Magelight\Components\Loaders\Config();
        $loader->loadConfig($app->getAppDir() . DS . 'etc' . DS . 'config.xml');
        $this->_config = $loader->getConfig();
    }

    /**
     * Load config for application modules
     *
     * @param \Magelight\App $app
     */
    public function loadModulesConfig(\Magelight\App $app)
    {
        $modulesConfig = $app->config()->getConfigBool('global/app/cache_modules_config')
            ? $this->cache()->get($this->buildCacheKey('modules_config'))
            : false;

        /* Loading modules config */
        if (!$modulesConfig) {
            $loader = new \Magelight\Components\Loaders\Config();
            $loader->setConfig($this->_config);
            foreach (array_reverse($app->getCodePools()) as $scope) {
                foreach ($app->modules()->getActiveModules() as $module) {
                    $filename = $app->getAppDir() . DS . 'modules' . DS . $scope . DS . $module['path']
                        . DS . 'etc' . DS . 'config.xml';
                    if (is_readable($filename)) {
                        $loader->loadConfig($filename);
                    }
                }
            }
            $modulesConfig = $loader->getConfig();
            if ($app->config()->getConfigBool('global/app/cache_modules_config')) {
                $this->cache()->set($this->buildCacheKey('modules_config'), $modulesConfig->asXML(), 360);
            }
            $this->_config = $loader->getConfig();
        } else {
            $this->_config = simplexml_load_string($modulesConfig);
        }
        unset($loader);
    }

    /**
     * Get merged configuration XML as string
     *
     * @return string
     */
    public function getConfigXmlString()
    {
        return $this->_config->asXML();
    }
    
    /**
     * Get configuration element by path (similar to xpath)
     * 
     * @param      $path
     * @param null $default
     *
     * @return array|null
     */
    public function getConfig($path, $default = null)
    {
        return $this->getConfigByPath($path, null, false, $default);
    }

    /**
     * Get config boolean value
     *
     * @param string $path
     * @param mixed $default
     * @return bool
     */
    public function getConfigBool($path, $default = null)
    {
        return (bool)(string)$this->getConfig($path, $default);
    }

    /**
     * Get config string value
     *
     * @param string $path
     * @param mixed $default
     * @return string
     */
    public function getConfigString($path, $default = null)
    {
        return (string)$this->getConfig($path, $default);
    }

    /**
     * Get config integer value
     *
     * @param string $path
     * @param mixed $default
     * @return int
     */
    public function getConfigInt($path, $default = null)
    {
        return (int)(string)$this->getConfig($path, $default);
    }

    /**
     * Get config float value
     *
     * @param string $path
     * @param mixed $default
     * @return float
     */
    public function getConfigFloat($path, $default = null)
    {
        return (float)(string)$this->getConfig($path, $default);
    }

    /**
     * Get config array value
     *
     * @param string $path
     * @param mixed $default
     * @return array
     */
    public function getConfigArray($path, $default = null)
    {
        return (array)(string)$this->getConfig($path, $default);
    }

    /**
     * Get configuration elements set by path
     *
     * @param      $path
     * @param null $default
     *
     * @return array|null
     */
    public function getConfigSet($path, $default = null)
    {
        return $this->getConfigByPath($path, null, true, $default);
    }

    /**
     * Get config first definition
     *
     * @param $path
     * @param string $type
     * @param null $default
     * @return array|bool|float|int|null|\SimpleXMLElement|string
     */
    public function getConfigFirst($path, $type = self::TYPE_STRING, $default = null)
    {
        $value = $this->getConfig($path);
        if (count($value) < 1) {
            return $default;
        }
        return $this->_getByType($value[0], $type);
    }

    /**
     * Get config last definition
     *
     * @param $path
     * @param string $type
     * @param null $default
     * @return array|bool|float|int|null|\SimpleXMLElement|string
     */
    public function getConfigLast($path, $type = self::TYPE_STRING, $default = null)
    {
        $value = $this->getConfig($path);
        if (count($value) < 1) {
            return $default;
        }
        return $this->_getByType(array_pop($value), $type);
    }

    /**
     * Turn  value to config type
     *
     * @param $value
     * @param string $type
     * @return array|bool|float|int|\SimpleXMLElement|string
     */
    protected function _getByType($value, $type = self::TYPE_STRING)
    {
        /* @var $value \SimpleXMLElement*/
        switch ($type) {
            case self::TYPE_STRING:
                return (string) $value;
                break;
            case self::TYPE_ARRAY:
                return (array) $value;
                break;
            case self::TYPE_BOOLEAN:
                return (bool) $value;
                break;
            case self::TYPE_INT:
                return (int) $value;
                break;
            case self::TYPE_FLOAT:
                return (float) $value;
                break;
            default:
                return $value;
        }
    }

    /**
     * Get configuration element attribute by path
     * 
     * @param      $path
     * @param      $attribute
     * @param null $default
     *
     * @return array|null
     */
    public function getConfigAttribute($path, $attribute, $default = null)
    {
        return $this->getConfigByPath($path, $attribute, false, $default);
    }

    /**
     * Get config element or attribute by path
     * 
     * @param      $path
     * @param null $attribute
     * @param bool $getSet
     * @param null $default
     *
     * @return mixed|null
     */
    protected function getConfigByPath($path, $attribute = null, $getSet = false, $default = null)
    {
        $path = self::CONFIG_PATH_PREFIX . ltrim($path, '\\/ ');
        $conf = $this->_config->xpath($path);
        if ($conf === false) {
            return $default;
        }
        if (empty($attribute)) {
            return $getSet ? $conf : (is_array($conf) ? array_pop($conf) : $conf);
        } else {
            return isset($conf[0]->$attribute) ? $conf[0]->$attribute : $default;
        }
    }
}
