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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight;
use Magelight\Components\Loaders\Config as ConfigLoader;
use Magelight\Traits\TForgery;

/**
 * Config wrapper
 *
 * @method static Config getInstance()
 */
class Config
{
    use \Magelight\Traits\TCache;

    use TForgery;

    /**
     * Config path prefix
     */
    const CONFIG_PATH_PREFIX = '//config/';

    /**
     * Config values types
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
    protected $config = null;

    /**
     * Load config for application modules
     *
     * @return void
     */
    public function load()
    {
        $app = \Magelight\App::getInstance();
        /** @var ConfigLoader $loader */
        $loader = ConfigLoader::getInstance();
        $this->loadConfigFromFile($app->getAppDir() . DS . 'etc' . DS . 'config.xml');
        $modulesConfigString = $this->getConfigBool('global/app/cache_modules_config', false)
            ? $this->cache()->get($this->buildCacheKey('modules_config'))
            : false;

        /* Loading modules config */
        if (!$modulesConfigString) {
            foreach (array_reverse($app->getModuleDirectories()) as $modulesDir) {
                foreach (\Magelight\Components\Modules::getInstance()->getActiveModules() as $module) {
                    $filename = $loader->getModulesConfigFilePath($modulesDir, $module);
                    if ($filename) {
                        $this->loadConfigFromFile($filename);
                    }
                }
            }
            if ($this->getConfigBool('global/app/cache_modules_config', false)) {
                $this->cache()->set($this->buildCacheKey('modules_config'), $this->config->asXML(), 3600);
            }
        } else {
            $this->config = simplexml_load_string($modulesConfigString);
        }
        unset($loader);
    }

    /**
     * Load config from file
     *
     * @param string $filename
     */
    public function loadConfigFromFile($filename)
    {
        $loader = ConfigLoader::getInstance();
        $loader->loadConfig($filename);
        $this->config = $loader->getConfig();
    }

    /**
     * Get merged configuration XML as string
     *
     * @return string
     */
    public function getConfigXmlString()
    {
        return $this->config->asXML();
    }
    
    /**
     * Get configuration element by path (similar to xpath)
     * 
     * @param      $path
     * @param null $default
     *
     * @return array|null|\SimpleXMLElement
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
    public function getConfigArray($path, $default = [])
    {
        $data = (array)$this->getConfig($path, $default);
        return (array)$data;
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
     * Get configuration element attribute by path
     * 
     * @param      $path
     * @param      $attribute
     * @param null $default
     *
     * @return string
     */
    public function getConfigAttribute($path, $attribute, $default = null)
    {
        return (string)$this->getConfigByPath($path, $attribute, false, $default);
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

        $conf = $this->config->xpath($path);
        if ($conf === false || is_array($conf) && empty($conf)) {
            return $default;
        }
        if (empty($attribute)) {
            return $getSet ? $conf : (is_array($conf) ? array_pop($conf) : $conf);
        } else {
            return isset($conf[0]->attributes()->$attribute) ? $conf[0]->attributes()->$attribute : $default;
        }
    }
}
