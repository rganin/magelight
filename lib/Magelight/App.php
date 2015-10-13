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
use Magelight\Traits\TForgery;

/**
 * Application class (no forgery available)
 */
abstract class App
{
    use TForgery;

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
     * Database objects
     *
     * @var array
     */
    protected $_dbs = [];

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
     * Get real path in modules
     *
     * @param $pathInModules
     * @return bool|string
     * @codeCoverageIgnore
     */
    public function getRealPathInModules($pathInModules)
    {
        foreach (array_reverse($this->getModuleDirectories()) as $directory) {
            if (is_readable($directory . DS . $pathInModules)) {
                return realpath($directory . DS . $pathInModules);
            }
        }
        return false;
    }

    /**
     * Add modules directory
     *
     * @param $directory
     * @return $this
     */
    public function addModulesDir($directory)
    {
        $this->_modulesDirectories[] = realpath($directory);
        return $this;
    }

    /**
     * Get modules directories
     *
     * @return \string[]
     */
    public function getModuleDirectories()
    {
        return $this->_modulesDirectories;
    }

    /**
     * Initialize include paths
     *
     * @throws Exception
     */
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
        \Magelight\Components\Modules::getInstance()->loadModules($this->getAppDir() . DS . 'etc' . DS . 'modules.xml');
        \Magelight\Config::getInstance()->load($this);
        $this->setDeveloperMode((string)$this->getConfig('global/app/developer_mode'));
        \Magelight\Http\Session::getInstance()->setSessionName(self::SESSION_ID_COOKIE_NAME)->start();
        $this->loadPreferences();
        $lang = \Magelight\Http\Session::getInstance()->get('lang');
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
     * Run app
     *
     * @throws \Exception|Exception
     */
    abstract public function run();

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
        $eventManager = \Magelight\Event\Manager::getInstance();
        $eventManager->dispatchEvent('app_dispatch_action', ['action' => $action, 'request' => $request]);
        $controllerName = str_replace('/', '\\', $action['module'] . '\\Controllers\\' . ucfirst($action['controller']));
        $controllerMethod = $action['action'] . 'Action';
        $controller = call_user_func([$controllerName, 'forge']);
        /* @var $controller \Magelight\Controller */
        $eventManager->dispatchEvent('app_controller_init', [
            'controller' => $controller,
            'action' => $action,
            'request' => $request
        ]);
        $controller->init($request, $action);
        $eventManager->dispatchEvent('app_controller_initialized', [
            'controller' => $controller,
            'action' => $action,
            'request' => $request
        ]);
        $controller->beforeExecute();
        $controller->$controllerMethod();
        $controller->afterExecute();
        $eventManager->dispatchEvent('app_controller_executed', [
            'controller' => $controller,
            'action' => $action,
            'request' => $request
        ]);
        return $this;
    }

    /**
     * Shutdown application handler
     * @codeCoverageIgnore
     */
    public function shutdown()
    {
        die();
    }

    /**
     * Load forgery preferences from configuration
     *
     * @return App
     */
    public function loadPreferences()
    {
        $preferenceList = \Magelight\Config::getInstance()->getConfigSet('global/forgery/preference');
        if (!empty($preferenceList)) {
            if (!is_array($preferenceList)) {
                $preferenceList = [$preferenceList];
            }
            foreach ($preferenceList as $preference) {

                if (!empty($preference->old) && !empty($preference->new)) {

                    self::getForgery()->setPreference(
                        trim($preference->old, " \\/ "),
                        trim($preference->new, " \\/ ")
                    );

                    if (!empty($preference->interface)) {
                        foreach ($preference->interface as $interface) {
                            self::getForgery()->addClassOverrideInterface(
                                (string)$preference->new, trim($interface, " \\/ ")
                            );
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
        return \Magelight\Config::getInstance()->getConfig($path, $default);
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
        if (!isset($this->_dbs[$index])) {
            $dbConfig = $this->getConfig('/global/db/' . $index, null);
            if (is_null($dbConfig)) {
                throw new \Magelight\Exception("Database `{$index}` configuration not found.");
            }
            $adapterClass = \Magelight\Db\Common\Adapter::getAdapterClassByType((string)$dbConfig->type);
            $db = call_user_func_array([$adapterClass, 'forge'], []);
            /* @var $db \Magelight\Db\Common\Adapter */
            $db->init((array)$dbConfig);
            $this->_dbs[$index] = $db;
        }
        return $this->_dbs[$index];
    }

    /**
     * Upgrade application
     *
     * @return App
     */
    public function upgrade()
    {
        foreach (\Magelight\Components\Modules::getInstance()->getActiveModules() as $module) {
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
            /* @var \Magelight\Cache\AdapterAbstract $adapter */
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
    protected function upgradeModule($module)
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
                mkdir(dirname($file), 0755, true);
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
            mkdir(dirname($file), 0755, true);
        }
        $scripts[$moduleName][basename($scriptFullPath)] = [date('Y-m-d H:i:s', time()), $scriptFullPath];
        $scripts = json_encode($scripts, JSON_PRETTY_PRINT);
        file_put_contents($file, $scripts);
        return $this;
    }
}
