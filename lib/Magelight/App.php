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

namespace Magelight;

class App
{
    /**
     * Session ID cookie name
     */
    const SESSION_ID_COOKIE_NAME = 'MAGELIGHTSSID';
    
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
     * Session cookie name
     *
     * @var string
     */
    protected $_sessionCookieName = self::SESSION_ID_COOKIE_NAME;
    
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
     * set session id cookie name
     */
    public function setSessionCookieName($name)
    {
        $this->_sessionCookieName = $name;
    }

    /**
     * Get session id cookie name
     *
     * @return string
     */
    public function getSessionCookieName()
    {
        return $this->_sessionCookieName;
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
        throw new \Magelight\Exception(
            'Trying to get undefined object from registry. Index: ' . print_r($index) . print_r(debug_backtrace())
        );
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
     * @return \Magelight\Components\Router
     */
    public function router()
    {
        return $this->getRegistryObject('router');
    }
    
    /**
     * Get application cache
     * 
     * @return \Magelight\Components\Cache
     */
    public function cache()
    {
        return $this->getRegistryObject('cache');
    }
    
    /**
     * Get application modules object
     * 
     * @return \Magelight\Components\Modules
     */
    public function modules()
    {
        return $this->getRegistryObject('modules');
    }
    
    /**
     * Get config object
     * 
     * @return \Magelight\Components\Config
     */
    public function config()
    {
        return $this->getRegistryObject('config');
    }
    
    /**
     * Get session object
     * 
     * @return \Magelight\Http\Session
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
        
        $this->setRegistryObject('cache', \Magelight\Components\Cache::getInstance());
        $this->cache()->init();
        
        $this->setRegistryObject('modules', new \Magelight\Components\Modules($this));
        $this->setRegistryObject('config', new \Magelight\Components\Config($this));
        $this->setRegistryObject('router', new \Magelight\Components\Router($this));
        $this->setRegistryObject('session', \Magelight\Http\Session::getInstance());
        $this->session()->setSessionName(self::SESSION_ID_COOKIE_NAME)->start();
        $this->loadClassesOverrides();
        
        return $this;
    }

    /**
     * Run app
     *
     * @param bool $muteExceptions
     * @throws \Exception|Exception
     */
    public function run($muteExceptions = true)
    {
        try {
            $request = new \Magelight\Http\Request();
            $action = $this->router()->getAction((string) $request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action, $request);
        } catch (\Magelight\Exception $e) {
            if (!$muteExceptions || $this->_developerMode) {
                \Magelight\Log::add($e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            if (!$muteExceptions || $this->_developerMode) {
                \Magelight\Log::add('Generic exception: ' . $e->getMessage());
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
        $controller = call_user_func(array($controllerName, 'forge'));
        /* @var $controller \Magelight\Controller*/
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
        $overrides = $this->config()->getConfig('global/forgery/override');
        if (!empty($overrides)) {
            foreach($overrides as $override) {
                if (!empty($override->old) && !empty($override->new)) {
                    \Magelight\Forgery\Forgery::addClassOverride(
                        trim($override->old, " \\/ "),
                        trim($override->new, " \\/ ")
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Upgrade application
     */
    public function upgrade()
    {
        foreach ($this->modules()->getActiveModules() as $module) {
            $this->upgradeModule($module['name']);
        }
    }

    /**
     * Upgrate module
     *
     * @param string $module
     */
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