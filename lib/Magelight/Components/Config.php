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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components;

class Config
{
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
        $loader = new \Magelight\Components\Loaders\Config();

        //@todo add config caching just as caching will be implemented
        $loader->loadConfig($app->getAppDir() . DS . 'etc' . DS . 'config.xml');
        
        foreach (array($app->getFrameworkDir(), $app->getAppDir()) as $dir) {
            foreach (array_keys($app->modules()->getActiveModules()) as $moduleName) {
                $filename = $dir . DS . 'modules' . DS . $moduleName . DS . 'etc' . DS . 'config.xml';
                if (file_exists($filename)) {
                    $loader->loadConfig($filename);
                }
            }
        }
        
        $this->_config = $loader->getConfig();
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
        return $this->getConfigByPath($path, null, $default);
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
        return $this->getConfigByPath($path, $attribute, $default);
    }

    /**
     * Get config element or attribute by path
     * 
     * @param      $path
     * @param null $attribute
     * @param null $default
     *
     * @return mixed|null
     */
    protected function getConfigByPath($path, $attribute = null, $default = null)
    {
        $path = self::CONFIG_PATH_PREFIX . ltrim($path, '\\/ ');
        $conf = $this->_config->xpath($path);
        if ($conf === false) {
            return $default;
        }
        if (empty($attribute)) {
            return $conf;
        } else {
            return isset($conf[0]->$attribute) ? $conf[0]->$attribute : $default;
        }
    }
}