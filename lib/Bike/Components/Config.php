<?php
/**
 * $$name_placeholder_notice$$
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

namespace Bike\Components;

class Config
{
    /**
     * @var array
     */
    protected $_config = array();
    
    /**
     * @var array
     */
    protected $_configPaths = array();
    
    /**
     * Application
     * 
     * @var \Bike\App
     */
    protected $_app = null;
    
    /**
     * Constructor
     * 
     * @param \Bike\App $app
     */
    public function __construct(\Bike\App $app)
    {
        $this->_app = $app;
        $loader = new \Bike\Components\Loaders\Config();
        
        $loader->loadConfig($app->getAppDir() . DS . 'etc' . DS . 'config.xml');
        
        foreach (array($app->getFrameworkDir(), $app->getAppDir()) as $dir) {
            foreach (array_keys($app->modules()->getModules()) as $moduleName) {
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
     * Build config attribute
     * 
     * @param      $path
     * @param null $attribute
     *
     * @return array
     */
    protected function buildConfigArrayPath($path, $attribute = null) 
    {
        
        $pathArray = explode('/', $path);
        $return = array();
        foreach ($pathArray as $pathItem) {
            array_push($return, $pathItem, \Bike\Helpers\XmlHelper::INDEX_CONTENT);
        }
        if (!empty($attribute)) {
            array_pop($return);
            array_push($return, \Bike\Helpers\XmlHelper::INDEX_ATTRIBUTES, $attribute);
        }
        return array_reverse($return);
    }

    /**
     * Get config element or attribute by path
     * 
     * @param      $path
     * @param null $attribute
     * @param null $default
     *
     * @return array|null
     */
    protected function getConfigByPath($path, $attribute = null, $default = null)
    {
        $path = trim($path, ' \\/');
        $cacheIndex = !empty($attribute) 
            ? \Bike\Helpers\XmlHelper::INDEX_ATTRIBUTES 
            : \Bike\Helpers\XmlHelper::INDEX_CONTENT;
        $pathArray = $this->buildConfigArrayPath($path, $attribute);
        
        $config = $this->_config;
        
        while (!empty($pathArray)) {
            $pathPart = array_pop($pathArray);
            if (isset($config[$pathPart])) {
                $config = $config[$pathPart];
            } else {
                return $default;
            }
        }
       
        $this->_configPaths[$cacheIndex][$path] = $config;
        return $config;
    }
}