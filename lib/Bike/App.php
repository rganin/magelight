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
     * Modules collection
     * 
     * @var array
     */
    protected $_modules = array();
    
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
            $routesLoader->parseRoutes($this->getAppDir() . 'modules' . DS . $moduleName . DS . 'config' . DS . 'routes.xml');
        }
        $this->router()->setRoutes($routesLoader->getRoutes());
        return $this;
    }
    
    public function loadConfig($filename)
    {
        return $this;
        
    }
    
    
    
    public function run($scope = self::DEFAULT_SCOPE, $muteExceptions = true)
    {
        try {
            $this->loadModules('modules.xml')->loadRoutes();
            $request = new \Bike\Http\Request();
            $action = $this->router($scope)->getAction((string) $request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action, $request);
        } catch (\Bike\Exception $e) {
            \Bike\Log::add($e->getMessage()); 
            if (!$muteExceptions) {
                throw $e;
            }
        } catch (\Exception $e) {
            if (!$muteExceptions) {
                throw $e;
            }
        }
    }
    
    protected function dispatchAction($action, $request)
    {
//        var_dump($action);
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