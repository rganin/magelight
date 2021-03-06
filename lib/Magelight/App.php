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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight;
use Magelight\Hook\HookFactory;
use Magelight\Traits\TForgery;

/**
 * Application class (no forgery available)
 *
 * @method static App getInstance()
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
    protected $currentAction = [];

    /**
     * Array of application code pools
     *
     * @var array
     */
    protected $pools = ['public'];

    /**
     * Modules directories
     *
     * @var string[]
     */
    protected $modulesDirectories = [];

    /**
     * Objects registry
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Application directory
     *
     * @var string
     */
    protected $appDir = './';

    /**
     * Framework directory
     *
     * @var string
     */
    protected $frameworkDir = null;

    /**
     * Is app in developer mode
     *
     * @var bool
     */
    protected $developerMode = false;

    /**
     * Is AOP enabled flag
     *
     * @var bool
     */
    protected $aopEnabled = false;

    /**
     * Session cookie name
     *
     * @var string
     */
    protected $sessionCookieName = self::SESSION_ID_COOKIE_NAME;

    /**
     * Application language ('en', 'ru', etc)
     *
     * @var string
     */
    protected $lang;

    /**
     * Database objects
     *
     * @var array
     */
    protected $dbs = [];

    /**
     * Get application directory
     *
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Set application dev mode
     *
     * @param bool $value
     * @return App
     */
    public function setDeveloperMode($value = true)
    {
        $this->developerMode = (bool)$value;
        return $this;
    }

    /**
     * Is app in developer mode
     *
     * @return bool
     */
    public function isInDeveloperMode()
    {
        return $this->developerMode;
    }

    /**
     * Set application directory
     *
     * @param string $directory
     * @return App
     */
    public function setAppDir($directory)
    {
        $this->appDir = $directory;
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
        $this->frameworkDir = $directory;
        return $this;
    }

    /**
     * Get framework directory
     *
     * @return string
     */
    public function getFrameworkDir()
    {
        return isset($this->frameworkDir) ? $this->frameworkDir : FRAMEWORK_DIR;
    }

    /**
     * Set application session cookie name
     *
     * @param string $name
     * @return App
     */
    public function setSessionCookieName($name)
    {
        $this->sessionCookieName = $name;
        return $this;
    }

    /**
     * Get session id cookie name
     *
     * @return string
     */
    public function getSessionCookieName()
    {
        return $this->sessionCookieName;
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
        $directory = realpath($directory);
        $this->modulesDirectories[$directory] = $directory;
        return $this;
    }

    /**
     * Get modules directories
     *
     * @return \string[]
     */
    public function getModuleDirectories()
    {
        return $this->modulesDirectories;
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
        \Magelight\Components\Modules::getInstance()->getActiveModules();
        \Magelight\Config::getInstance()->load();
        $this->loadPreferences();
        HookFactory::getInstance()->loadHooksConfig();
        $this->setDeveloperMode((string)\Magelight\Config::getInstance()->getConfig('global/app/developer_mode'));
        if ($this->isInDeveloperMode()) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }
        \Magelight\Http\Session::getInstance()
            ->setLifetime((int)\Magelight\Config::getInstance()->getConfig('global/app/session_lifetime', 1440))
            ->setSessionName(self::SESSION_ID_COOKIE_NAME)
            ->start();
        $lang = \Magelight\Http\Session::getInstance()->get('lang');
        if (empty($lang)) {
            $lang = (string)\Magelight\Config::getInstance()->getConfig('global/app/default_lang');
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
        $this->lang = $lang;
        if (!empty($this->lang)) {
            \Magelight\I18n\Translator::getInstance()->loadTranslations($this->lang);
        }
    }

    /**
     * Get application language
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
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
        return $this->currentAction;
    }

    /**
     * Dispatch action
     *
     * @param array $action
     *
     * @return App
     * @throws \Magelight\Exception
     */
    public function dispatchAction(array $action)
    {
        $this->currentAction = $action;
        $eventManager = \Magelight\Event\Manager::getInstance();
        $request = \Magelight\Http\Request::getInstance();
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
        $controller->init($action);
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
        \Magelight\Http\Session::getInstance()->close();
    }

    /**
     * Load forgery preferences from configuration
     *
     * @return App
     * @codeCoverageIgnore
     */
    public function loadPreferences()
    {
        $preferenceList = (array)\Magelight\Config::getInstance()->getConfigSet('global/forgery/preference');
        self::getForgery()->loadPreferences($preferenceList);
        return $this;
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
        if (!isset($this->dbs[$index])) {
            $dbConfig = \Magelight\Config::getInstance()->getConfig('/global/db/' . $index, null);
            if (is_null($dbConfig)) {
                throw new \Magelight\Exception("Database `{$index}` configuration not found.");
            }
            $adapterClass = \Magelight\Db\Common\Adapter::getAdapterClassByType((string)$dbConfig->type);
            $db = call_user_func_array([$adapterClass, 'forge'], []);
            /* @var $db \Magelight\Db\Common\Adapter */
            $db->init((array)$dbConfig);
            $this->dbs[$index] = $db;
        }
        return $this->dbs[$index];
    }

    /**
     * Flush all application caches
     *
     * @return App
     */
    public function flushAllCache()
    {
        $config = \Magelight\Config::getInstance()->getConfig('global/cache');
        foreach ($config->children() as $index => $cache) {
            $adapters[] = \Magelight\Cache\AdapterPool::getInstance()->getAdapter($index)->clear();
        }
        return $this;
    }
}
