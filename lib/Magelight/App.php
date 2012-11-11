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

final class App
{
    /**
     * Session ID cookie name
     */
    const SESSION_ID_COOKIE_NAME = 'MAGELIGHTSSID';

    /**
     * Default database index
     */
    const DEFAULT_DB_INDEX = 'default';

    /**
     * Classes overrides
     *
     * @var array
     */
    protected $_classOverrides = [];

    /**
     * Objects registry
     * 
     * @var array
     */   
    protected $_registry = [];
        
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
    protected $_frameworkDir = FRAMEWORK_DIR;
    
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
     * Get framework directory
     * 
     * @return string
     */
    public function getFrameworkDir()
    {
        return $this->_frameworkDir;
    }

    /**
     * Set application session cookie name
     *
     * @param string $name
     * @return App
     */
    public function setSessionCookieName($name)
    {
        $this->_sessionCookieName = $name;
        return $this;
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
     * Get object from app registry
     *
     * @param string $index
     * @param mixed $default
     * @return mixed
     */
    public function getRegistryObject($index, $default = null)
    {
        if (isset($this->_registry[$index]) && !empty($this->_registry[$index])) {
            return $this->_registry[$index];
        } else {
            return $default;
        }
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
        $this->_frameworkDir = FRAMEWORK_DIR;
        $includePath = explode(PS, ini_get('include_path'));
        array_unshift(
            $includePath, 
            $this->_frameworkDir . DS . 'lib',
            $this->_appDir . DS . 'modules' . DS . 'public',
            $this->_appDir . DS . 'modules' . DS . 'private'
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
        } catch (\Exception $e) {
            \Magelight\Log::add($e->getMessage());
            if (!$muteExceptions || $this->_developerMode) {
                throw $e;
            }
        }
    }

    /**
     * Dispatch action
     * 
     * @param array $action
     * @param \Magelight\Http\Request $request
     *
     * @return App
     * @throws \Magelight\Exception
     */
    public function dispatchAction(array $action, \Magelight\Http\Request $request = null)
    {
        $controllerName = $action['module'] . '\\Controllers\\' . ucfirst($action['controller']);
        $controllerMethod = $action['action'] . 'Action';
        $controller = call_user_func(array($controllerName, 'forge'));
        /* @var $controller \Magelight\Controller*/

        if ($this->isInDeveloperMode() && !is_callable([$controller, $controllerMethod])) {
            $controllerName = get_class($controller);
            throw new \Magelight\Exception(
                "Trying to run undefined controller action {$controllerMethod} in {$controllerName}"
            );
        }
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
            if (!is_array($overrides)) {
                $overrides = [$overrides];
            }
            foreach($overrides as $override) {
                if (!empty($override->old) && !empty($override->new)) {
                    static::addClassOverride(
                        trim($override->old, " \\/ "),
                        trim($override->new, " \\/ ")
                    );
                }
            }
        }
        return $this;
    }

    /**
     * get config element by path
     *
     * @param $path
     * @param null $default
     * @return \SimpleXMLElement|mixed
     */
    public function getConfig($path, $default = null)
    {
        return $this->config()->getConfig($path, $default);
    }

    /**
     * Add message to log
     *
     * @param string $logMessage
     */
    public function log($logMessage)
    {
        \Magelight\Log::add($logMessage);
    }

    /**
     * Get database
     *
     * @param string $index
     * @return Dbal\Db\Common\Adapter
     * @throws Exception
     */
    public function db($index = self::DEFAULT_DB_INDEX)
    {
        $db = $this->getRegistryObject('database/' . $index);

        if (!$db instanceof \Magelight\Dbal\Db\Common\Adapter) {
            $dbConfig = $this->getConfig('/global/db/' . $index, null);
            if (is_null($dbConfig)) {
                throw new \Magelight\Exception("Database {$index} configuration not found.");
            }
            $adapterClass = \Magelight\Dbal\Db\Common\Adapter::getAdapterClassByType((string) $dbConfig->type);
            $db = new $adapterClass();
            /* @var $db \Magelight\Dbal\Db\Common\Adapter*/
            $db->init((array) $dbConfig);
            $this->setRegistryObject('database/' . $index, $db);
        }
        return $db;
    }

    /**
     * Get class name according to runtime overrides
     *
     * @param string $className
     * @return mixed
     */
    final public function getClassName($className)
    {
        while (!empty($this->_classOverrides[$className])) {
            $className = $this->_classOverrides[$className];
        }
        return $className;
    }

    /**
     * Add class to override
     *
     * @static
     * @param string $sourceClassName
     * @param string $replacementClassName
     */
    final public function addClassOverride($sourceClassName, $replacementClassName)
    {
        $this->_classOverrides[$sourceClassName] = $replacementClassName;
    }

//    /**
//     * Upgrade application
//     */
//    public function upgrade()
//    {
//        foreach ($this->modules()->getActiveModules() as $module) {
//            $this->upgradeModule($module['name']);
//        }
//    }
//
//    /**
//     * Upgrate module
//     *
//     * @param string $module
//     */
//    public function upgradeModule($module)
//    {
//        $file = $this->getAppDir() . '/modules/' . $module . "/upgrade/install.php";
//        if (file_exists($file)) {
//            include $file;
//                if(rename($file, $file . ".complete")) {
//            echo "renaming $file";
//            } else {
//            echo "error renaming $file";
//            }
//        }
//    }
}
