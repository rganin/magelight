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
     * Session ID cookie name
     */
    const SESSION_ID_COOKIE_NAME = 'BIKESSID';
    
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
     * Framework directory
     * 
     * @var string
     */
    protected $_frameworkDir = '../';
    
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
     * @param string $directory
     * @return App
     */
    public function setAppDir($directory)
    {
        $this->_appDir = $directory;
        return $this;
    }
    
    /**
     * Set framework directory
     * 
     * @param string $directory
     * @return App
     */
    public function setFrameworkDir($directory)
    {
        $this->_frameworkDir = $directory;
        return $this;
    }
    
    /**
     * Get framework directory
     * 
     * @return string
     */
    public function getFrameworkDir()
    {
        return $this->_frameworkDir;
    }

    /**
     * Get object from registry
     * 
     * @param string $index
     *
     * @return mixed
     * @throws Exception
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
     * Get session object
     * 
     * @return \Bike\Http\Session
     */
    public function session()
    {
        return $this->getRegistryObject('session');
    }

    /**
     * Initialize application
     * 
     * @return App
     */
    public function init()
    {
        $includePath = explode(PS, ini_get('include_path'));
        array_unshift(
            $includePath, 
            $this->_frameworkDir . DS . 'lib',
            $this->_frameworkDir . DS . 'modules',
            $this->_appDir . DS . 'modules'
        );
        
        ini_set('include_path', implode(PS, $includePath));
        
        $this->setRegistryObject('cache', \Bike\Components\Cache::getInstance());
        $this->cache()->init();
        
        $this->setRegistryObject('modules', new \Bike\Components\Modules($this));
        $this->setRegistryObject('config', new \Bike\Components\Config($this));
        $this->setRegistryObject('router', new \Bike\Components\Router($this));
        $this->setRegistryObject('session', \Bike\Http\Session::getInstance());
        $this->session()->setSessionName(self::SESSION_ID_COOKIE_NAME)->start();
        $this->loadClassesOverrides();
        
        return $this;
    }

    /**
     * Run application
     * 
     * @param bool $muteExceptions
     *
     * @throws Exception
     * @throws \Exception
     */
    public function run($muteExceptions = true)
    {
        try {
            $request = new \Bike\Http\Request();
            $action = $this->router()->getAction((string) $request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action, $request);
        } catch (\Bike\Exception $e) {
            if (!$muteExceptions || $this->_developerMode) {
                \Bike\Log::add($e->getMessage()); 
                throw $e;
            }
        } catch (\Exception $e) {
            if (!$muteExceptions || $this->_developerMode) {
                \Bike\Log::add('Generic exception: ' . $e->getMessage());
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
        $controller = call_user_func(array($controllerName, 'create'));
        /* @var $controller \Bike\Controller*/
        $controller->init($request);
        $controller->beforeExecute();
        $controller->$controllerMethod();
        $controller->afterExecute();
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
                    \Bike\Prototypes\Overridable::addClassOverride(
                        trim($override[$contentIndex]['class'][$contentIndex], " \\/"),
                        trim($override[$contentIndex]['replace'][$contentIndex], " \\/")
                    );
                }
            }
        }
        return $this;
    }
    
    public function upgrade()
    {
        foreach ($this->modules()->getModules() as $module) {
            $this->upgradeModule($module['name']);
        }
    }
    
    public function upgradeModule($module)
    {
        $file = $this->getAppDir() . '/modules/' . $module . "/upgrade/install.php";
        if (file_exists($file)) {
            include $file; 
                if(rename($file, $file . ".complete")) {
            echo "renaming $file";
            } else {
            echo "error renaming $file";    
            }
        }
    }
}