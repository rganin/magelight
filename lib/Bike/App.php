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
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike;

class App
{
    /**
     * Default scope
     */
    const DEFAULT_SCOPE = 'default';
    
    /**
     * Documents array
     * 
     * @var array[\Html\Document]
     */
    protected $_documents = array();
    
    /**
     * Application routers
     * 
     * @var array
     */
    protected $_routers = array();
    
    /**
     * Application directory
     * 
     * @var string
     */
    protected $_appDir = './';
    
    /**
     * Configuration
     * 
     * @var array
     */
    protected $_config = array();

    /**
     * Config elements by path
     * 
     * @var array
     */
    protected $_configPaths = array();
    
    /**
     * Modules collection
     * 
     * @var array
     */
    protected $_modules = array();

    /**
     * Is app in developer mode
     *
     * @var bool
     */
    protected $_developerMode = false;
    
    /**
     * Get application directory
     * 
     * @return string
     */
    public function getAppDir()
    {
        return $this->_appDir;
    }

    /**
     * Set application dev mode
     *
     * @param bool $value
     * @return App
     */
    public function setDeveloperMode($value = true)
    {
        $this->_developerMode = (bool) $value;
        return $this;
    }

    /**
     * Is app in developer mode
     *
     * @return bool
     */
    public function isInDeveloperMode()
    {
        return $this->_developerMode;
    }
    
    /**
     * Set application directory
     * 
     * @param $directory
     * @return App
     */
    public function setAppDir($directory)
    {
        $this->_appDir = $directory . DIRECTORY_SEPARATOR;
        return $this;
    }
    
    /**
     * Get document
     * 
     * @param string $index
     * @return \Bike\Html\Document
     */
    public function document($index = self::DEFAULT_SCOPE)
    {
        if (!isset($this->_documents[$index])) {
            $this->_documents[$index] = new \Bike\Html\Document();
        }
        return $this->_documents[$index];
    }
    
    /**
     * Get router
     * 
     * @param string $index
     * @return \Bike\Router
     */
    public function router($index = self::DEFAULT_SCOPE)
    {
        if (!isset($this->_routers[$index])) {
            $this->_routers[$index] = new \Bike\Router();
        }
        return $this->_routers[$index];
    }
    
    /**
     * Load modules from file
     * 
     * @param $modulesXmlFilename
     * @return \Bike\App
     */
    public function loadModules($modulesXmlFilename)
    {
        $xml = simplexml_load_file($modulesXmlFilename);
        $modulesLoader = new \Bike\Loaders\Modules($xml);
        $this->_modules = $modulesLoader->getActiveModules();
        unset($modulesLoader);
        return $this;
    }
    
    /**
     * Load routes by modules
     * 
     * @return App
     */
    public function loadRoutes()
    {
        $routesLoader = new \Bike\Loaders\Routes();
        foreach (array_keys($this->_modules) as $moduleName) {
            $filename = $this->getAppDir() . 'modules' . DS . $moduleName . DS . 'etc' . DS . 'routes.xml';
            if (file_exists($filename)) {
                $routesLoader->parseRoutes($filename);
            }
        }
        $this->router()->setRoutes($routesLoader->getRoutes());
        unset($routesLoader);
        return $this;
    }

    /**
     * Load configuration
     * 
     * @return App
     */
    public function loadConfig()
    {
        $configLoader = new \Bike\Loaders\Config();
        $configLoader->loadConfig($this->getAppDir() . 'config.xml');
        foreach (array_keys($this->_modules) as $moduleName) {
            $filename = $this->getAppDir() . 'modules' . DS . $moduleName . DS . 'etc' . DS . 'config.xml';
            if (file_exists($filename)) {
                $configLoader->loadConfig($filename);
            }
        }
        $this->_config = $configLoader->getConfig();
        unset($configLoader);
        return $this;
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

    /**
     * Initialize application
     * 
     * @return App
     */
    public function init()
    {
        $this->loadModules('modules.xml')->loadRoutes()->loadConfig();
        return $this;
    }

    /**
     * Run application
     * 
     * @param string $scope
     * @param bool   $muteExceptions
     *
     * @throws Exception
     * @throws \Exception
     */
    public function run($scope = self::DEFAULT_SCOPE, $muteExceptions = true)
    {
        try {
            $request = new \Bike\Http\Request();
            $action = $this->router($scope)->getAction((string) $request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action, $request);
        } catch (\Bike\Exception $e) {
            \Bike\Log::add($e->getMessage()); 
            if (!$muteExceptions || $this->_developerMode) {
                throw $e;
            }
        } catch (\Exception $e) {
            \Bike\Log::add('Generic exception: ' . $e->getMessage()); 
            if (!$muteExceptions || $this->_developerMode) {
                throw $e;
            }
        }
    }

    /**
     * Dispatch action
     * 
     * @param $action
     * @param $request
     *
     * @return App
     */
    protected function dispatchAction($action, $request)
    {
        $controllerName = $action['module'] . '\\Controllers\\' . ucfirst($action['controller']);
        $controllerMethod = $action['action'] . 'Action';
        $controller = new $controllerName($request, $this);
        /* @var $controller \Bike\Controller*/
        $controller->beforeExec();
        $controller->$controllerMethod();
        $controller->afterExec();
        return $this;
    }
}