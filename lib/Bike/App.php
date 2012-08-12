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

namespace Bike;

class App
{

    
    /**
     * Objects registry
     * 
     * @var array
     */   
    protected $_registry = array();
        
    /**
     * Application directory
     * 
     * @var string
     */
    protected $_appDir = './';
    
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
     * Get object from app registry
     * 
     * @param $index
     * @return mixed
     */
    public function getRegistryObject($index)
    {
        if (isset($this->_registry[$index]) && !empty($this->_registry[$index])) {
            return $this->_registry[$index];
        }
        throw new \Bike\Exception('Trying to get undefined object from registry. Index: ' . print_r($index) . print_r(debug_backtrace()));
    }
    
    /**
     * Set object to registry by index
     * 
     * @param string $index
     * @param mixed $object
     * @return App
     */
    public function setRegistryObject($index, $object)
    {
        $this->_registry[$index] = $object;
        return $this;
    }
    
    /**
     * Get document
     * 
     * @return \Bike\Html\Document
     */
    public function document()
    {
        return $this->getRegistryObject('document');
    }
    
    /**
     * Get router
     * 
     * @return \Bike\Components\Router
     */
    public function router()
    {
        return $this->getRegistryObject('router');
    }
    
    /**
     * Get application cache
     * 
     * @return \Bike\Components\Cache
     */
    public function cache()
    {
        return $this->getRegistryObject('cache');
    }
    
    /**
     * Get application modules object
     * 
     * @return \Bike\Components\Modules
     */
    public function modules()
    {
        return $this->getRegistryObject('modules');
    }
    
    /**
     * Get config object
     * 
     * @return \Bike\Components\Config
     */
    public function config()
    {
        return $this->getRegistryObject('config');
    }

    /**
     * Initialize application
     * 
     * @return App
     */
    public function init()
    {
        $cache = \Bike\Components\Cache::getInstance();
        /* @var \Bike\Components\Cache $cache */
        $cache->init();
        
        $this->setRegistryObject('modules', new \Bike\Components\Modules($this));
        $this->setRegistryObject('config', new \Bike\Components\Config($this));
        $this->setRegistryObject('router', new \Bike\Components\Router($this));
        
        $this->loadClassesOverrides();
        
        return $this;
    }

    /**
     * Run application
     * 
     * @param bool   $muteExceptions
     *
     * @throws Exception
     */
    public function run($muteExceptions = true)
    {
        try {
            
            $request = new \Bike\Http\Request();
            $action = $this->router()->getAction((string) $request->getRequestRoute());
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
    
    /**
     * Load classes overrides from configuration
     * 
     * @return App
     */
    public function loadClassesOverrides()
    {
        $overrides = $this->config()->getConfig('global/classes/override');
        if (!empty($overrides)) {
            $contentIndex = \Bike\Helpers\XmlHelper::INDEX_CONTENT;
            foreach($overrides as $override) {
                if (!empty($override[$contentIndex]['class'][$contentIndex]) 
                    && !empty($override[$contentIndex]['replace'][$contentIndex])) {
                    \Bike::addClassOverride(
                        trim($override[$contentIndex]['class'][$contentIndex], " \\/"),
                        trim($override[$contentIndex]['replace'][$contentIndex], " \\/")
                    );
                }
            }
        }
        return $this;
    }
}