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

namespace Magelight;

/**
 * Application class (no forgery available)
 */
class App
{
    /**
     * Session ID cookie name
     */
    const SESSION_ID_COOKIE_NAME = 'MAGELIGHTSSID';

    /**
     * Default database index
     */
    const DEFAULT_INDEX = 'default';

    /**
     * Default application language
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * Current Action
     *
     * @var array
     */
    protected $_currentAction = [];

    /**
     * Array of application code pools
     *
     * @var array
     */
    protected $_pools = ['public'];

    /**
     * Modules directories
     *
     * @var string[]
     */
    protected $_modulesDirectories = [];

    /**
     * Classes overrides
     *
     * @var array
     */
    protected $_classOverrides = [];

    /**
     * Interfaces for classes overrides
     *
     * @var array
     */
    protected $_classOverridesInterfaces = [];

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
    protected $_frameworkDir = null;

    /**
     * Is app in developer mode
     *
     * @var bool
     */
    protected $_developerMode = false;

    /**
     * Is AOP enabled flag
     *
     * @var bool
     */
    protected $_aopEnabled = false;

    /**
     * Session cookie name
     *
     * @var string
     */
    protected $_sessionCookieName = self::SESSION_ID_COOKIE_NAME;

    /**
     * Application language ('en', 'ru', etc)
     *
     * @var string
     */
    protected $_lang;

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
        $this->_developerMode = (bool)$value;
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
     * Set App framework directory
     *
     * @param string $directory
     * @return App
     */
    public function setFrameworkDir($directory = FRAMEWORK_DIR)
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
        return isset($this->_frameworkDir) ? $this->_frameworkDir : FRAMEWORK_DIR;
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
     * Get request singleton object
     *
     * @return \Magelight\Http\Request
     */
    public function request()
    {
        return $this->getRegistryObject('request');
    }

    /**
     * Get cache adapter
     *
     * @param string $index
     * @return Cache\AdapterAbstract
     */
    public function cache($index = self::DEFAULT_INDEX)
    {
        return \Magelight\Cache\AdapterAbstract::getAdapterInstance($index);
    }

    /**
     * Set application code pools (the sequence is an include path sequence,
     * so classes from the first code pool in the array will be included first)
     *
     * @param array $pools
     * @return App
     */
    public function setCodePools($pools = ['public'])
    {
        $this->_pools = $pools;
        return $this;
    }

    /**
     * Get application code pools in loading sequence
     *
     * @return array
     */
    public function getCodePools()
    {
        return $this->_pools;
    }

    public function getRealPathInModules($pathInModules)
    {
        foreach (array_reverse($this->getModuleDirectories()) as $directory) {
            if (is_readable($directory . DS . $pathInModules)) {
                return realpath($directory . DS . $pathInModules);
            }
        }
        return false;
    }

    public function addModulesDir($directory)
    {
        $this->_modulesDirectories[] = realpath($directory);
        return $this;
    }

    public function getModuleDirectories()
    {
        return $this->_modulesDirectories;
    }

    protected function initIncludePaths()
    {
        $includePath = explode(PS, ini_get('include_path'));
        foreach (array_reverse($this->getModuleDirectories()) as $directory) {
            if (!is_readable($directory)) {
                throw new Exception("Modules directory {$directory} does not exist or is not readable.");
            }
            array_unshift($includePath, realpath($directory));
        }
        ini_set('include_path', implode(PS, $includePath));
    }

    /**
     * Initialize application
     *
     * @return App
     * @throws \Magelight\Exception
     */
    public function init()
    {
        $this->addModulesDir($this->getFrameworkDir() . DS . 'modules');
        $this->initIncludePaths();
        $this->setRegistryObject('modules', new \Magelight\Components\Modules($this));
        $this->setRegistryObject('config', new \Magelight\Components\Config($this));
        $this->setDeveloperMode((string)$this->getConfig('global/app/developer_mode'));
        $this->setAopEnabled((bool)$this->config()->getConfigBool('global/app/aop_enabled', false));
        if ($this->isAopEnabled()) {
            $this->_loadAspects();
        }
        $this->config()->loadModulesConfig($this);
        $this->setRegistryObject('router', new \Magelight\Components\Router($this));
        $this->setRegistryObject('session', \Magelight\Http\Session::getInstance());
        $this->session()->setSessionName(self::SESSION_ID_COOKIE_NAME)->start();
        $this->loadClassesOverrides();
        $lang = $this->session()->get('lang');
        if (empty($lang)) {
            $lang = (string)$this->getConfig('global/app/default_lang');
        }
        if (empty($lang)) {
            $lang = self::DEFAULT_LANGUAGE;
        }
        $this->setLang($lang);
        return $this;
    }

    /**
     * Set application language
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->_lang = $lang;
        if (!empty($this->_lang)) {
            \Magelight\I18n\Translator::getInstance()->loadTranslations($this->_lang);
        }
    }

    /**
     * Get application language
     *
     * @return string
     */
    public function getLang()
    {
        return $this->_lang;
    }

    /**
     * Load application aspects
     */
    protected function _loadAspects()
    {
        foreach ($this->getConfig('global/app/aspects') as $aspect) {
            foreach ($aspect->pointcut->point as $point) {
                \Magelight\Aop\Kernel::getInstance()->registerAspect((string)$point, (string)$aspect->advice);
            }
        }
    }

    /**
     * Set AOP enabled
     *
     * @param bool $flag
     * @return App
     */
    public function setAopEnabled($flag = false)
    {
        $this->_aopEnabled = $flag;
        return $this;
    }

    /**
     * Check is AOP enabled for application
     *
     * @return bool
     */
    public function isAopEnabled()
    {
        return $this->_aopEnabled;
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
            $this->fireEvent('app_start', ['muteExceptions' => $muteExceptions]);
            $request = \Magelight\Http\Request::getInstance();
            $this->setRegistryObject('request', $request);
            $action = $this->router()->getAction((string) $request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action, $request);
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if (!$muteExceptions || $this->_developerMode) {
                throw $e;
            }
        }
    }

    /**
     * Get current application dispatched action
     *
     * @return array
     */
    public function getCurrentAction()
    {
        return $this->_currentAction;
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
        $this->_currentAction = $action;
        $this->fireEvent('app_dispatch_action', ['action' => $action, 'request' => $request]);
        $controllerName = str_replace('/','\\', $action['module'] . '\\Controllers\\' . ucfirst($action['controller']));
        $controllerMethod = $action['action'] . 'Action';
        if ($this->isInDeveloperMode()) {
            if (!@include_once(\Magelight::getAutoloaderFileNameByClass($controllerName))) {
                throw new \Magelight\Exception(
                    "Unable to load controller {$controllerName} for route {$action['match']} "
                    . "in module {$action['module']}."
                );
            }
        }
        $controller = call_user_func(array($controllerName, 'forge'));
        /* @var $controller \Magelight\Controller*/

        if ($this->isInDeveloperMode() && !is_callable([$controller, $controllerMethod])) {
            throw new \Magelight\Exception(
                "Trying to run undefined controller action {$controllerMethod} in {$controllerName}"
            );
        }
        $this->fireEvent('app_controller_init', [
            'controller' => $controller,
            'action'     => $action,
            'request'    => $request
        ]);
        $controller->init($request, $action);
        $this->fireEvent('app_controller_initialized', [
            'controller' => $controller,
            'action'     => $action,
            'request'    => $request
        ]);
        $controller->beforeExecute();
        $controller->$controllerMethod();
        $controller->afterExecute();
        $this->fireEvent('app_controller_executed', [
            'controller' => $controller,
            'action'     => $action,
            'request'    => $request
        ]);
        return $this;
    }

    /**
     * Shutdown application handler
     */
    public function shutdown()
    {
        die();
    }
    
    /**
     * Load classes overrides from configuration
     * 
     * @return App
     */
    public function loadClassesOverrides()
    {
        $overrides = $this->config()->getConfigSet('global/forgery/override');
        if (!empty($overrides)) {
            if (!is_array($overrides)) {
                $overrides = [$overrides];
            }
            foreach($overrides as $override) {

                if (!empty($override->old) && !empty($override->new)) {

                    $this->addClassOverride(
                        trim($override->old, " \\/ "),
                        trim($override->new, " \\/ ")
                    );

                    if (!empty($override->interface)) {
                        foreach ($override->interface as $interface) {
                            $this->addClassOverrideInterface((string) $override->new, trim($interface, " \\/ "));
                        }
                    }
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
        \Magelight\Log::getInstance()->add($logMessage);
    }

    /**
     * Get database
     *
     * @param string $index
     * @return Db\Common\Adapter
     * @throws Exception
     */
    public function db($index = self::DEFAULT_INDEX)
    {
        $db = $this->getRegistryObject('database/' . $index);

        if (!$db instanceof \Magelight\Db\Common\Adapter) {
            $dbConfig = $this->getConfig('/global/db/' . $index, null);
            if (is_null($dbConfig)) {
                throw new \Magelight\Exception("Database `{$index}` configuration not found.");
            }
            $adapterClass = \Magelight\Db\Common\Adapter::getAdapterClassByType((string) $dbConfig->type);
            $db = new $adapterClass();
            /* @var $db \Magelight\Db\Common\Adapter*/
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
     * Add class interface check for overriden class
     *
     * @param string $className
     * @return array
     */
    final public function getClassInterfaces($className)
    {
        return !empty($this->_classOverridesInterfaces[$className])
            && is_array($this->_classOverridesInterfaces[$className])
            ? $this->_classOverridesInterfaces[$className]
            : [];
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

    /**
     * Add interface check to overriden class
     *
     * @static
     * @param string $className
     * @param string $interfaceName
     */
    final public function addClassOverrideInterface($className, $interfaceName)
    {
        if (!isset($this->_classOverridesInterfaces[$className])) {
            $this->_classOverridesInterfaces[$className] = [];
        }
        $this->_classOverridesInterfaces[$className][] = $interfaceName;
    }

    /**
     * Upgrade application
     *
     * @return App
     */
    public function upgrade()
    {
        foreach ($this->modules()->getActiveModules() as $module) {
            $this->upgradeModule($module);
        }
        return $this;
    }

    /**
     * Flush all application caches
     *
     * @return App
     */
    public function flushAllCache()
    {
        foreach (\Magelight\Cache\AdapterAbstract::getAllAdapters() as $adapter) {
            /* @var \Magelight\Cache\AdapterAbstract $adapter*/
            $adapter->clear();
        }
        return $this;
    }

    /**
     * Upgrade module
     *
     * @param array $module
     * @return App
     */
    protected  function upgradeModule($module)
    {
        $installer = Installer::forge();
        $scripts = $installer->findInstallScripts($module['path']);
        foreach ($scripts as $script) {
            if (!$this->isSetupScriptExecuted($module['name'], $script)) {
                $installer->executeScript($script);
                $this->setSetupScriptExecuted($module['name'], $script);
            }
        }
        unset($installer);
        return $this;
    }

    /**
     * Check was setup script executed before
     *
     * @param string $moduleName
     * @param string $scriptName
     * @return bool
     */
    protected function isSetupScriptExecuted($moduleName, $scriptName)
    {
        $file = $this->getAppDir()
            . DS
            . $this->getConfig('global/setup/executed_scripts/filename', 'var/executed_setup.json');

        if (!file_exists($file)) {
            if (!file_exists(dirname($file))) {
                mkdir(dirname($file), 755, true);
            }
            file_put_contents($file, '');
        }
        $scripts = json_decode(file_get_contents($file), true);
        return isset($scripts[$moduleName][basename($scriptName)]);
    }

    /**
     * Set script as executed
     *
     * @param string $moduleName
     * @param string $scriptFullPath
     * @return App
     */
    protected function setSetupScriptExecuted($moduleName, $scriptFullPath)
    {
        $file = $this->getAppDir()
            . DS
            . $this->getConfig('global/setup/executed_scripts/filename', 'var/executed_setup.json');

        if (file_exists($file)) {
            $scripts = json_decode(file_get_contents($file), true);
        } else {
            mkdir(dirname($file), 755, true);
        }
        $scripts[$moduleName][basename($scriptFullPath)] = [date('Y-m-d H:i:s', time()), $scriptFullPath];
        $scripts = json_encode($scripts, JSON_PRETTY_PRINT);
        file_put_contents($file, $scripts);
        return $this;
    }

    /**
     * Fetch url by match mask
     *
     * @param string $match - url match mask
     * @param array $params - params to be passed to URL
     * @param string $type - URL type (http|https)
     * @param bool $addOnlyMaskParams - add to url only params that are present in URL match mask
     *
     * @return string
     */
    public function url($match, $params = [], $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP, $addOnlyMaskParams = false)
    {
        $url = \Magelight\Helpers\UrlHelper::getInstance()->getUrl($match, $params, $type, $addOnlyMaskParams);
        return $url;
    }

    /**
     * Fire application event (executes all observers that were bound to this event)
     *
     * @param string $eventName
     * @param array $arguments
     * @throws \Magelight\Exception
     */
    public function fireEvent($eventName, $arguments = [])
    {
        $observers = (array)$this->config()->getConfigSet('global/events/' . $eventName . '/observer');
        if (!empty($observers)) {
            foreach ($observers as $observerClass) {
                $observerClass = explode('::', (string)$observerClass);
                $class = $observerClass[0];
                $method = isset($observerClass[1]) ? $observerClass[1] : 'execute';
                $observer = call_user_func_array([$class, 'forge'], [$arguments]);
                /* @var $observer \Magelight\Observer*/
                if (!is_callable([$observer, $method])) {
                    throw new Exception("Observer '{$class}' method '{$method}' does not exist or is not callable!");
                }
                $observer->$method();
            }
        }
    }
}
