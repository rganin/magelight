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
 * Abstract application controller
 *
 * @method static \Magelight\Controller forge()
 */
abstract class Controller
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Use cacher trait
     */
    use \Magelight\Traits\TCache;

    /**
     * Use coalesce trait
     */
    use Traits\TCoalesce;

    /**
     * Default controller action
     */
    const DEFAULT_ACTION = 'index';

    /**
     * Default token key in session
     */
    const DEFAULT_TOKEN_SESSION_INDEX = 'session_token';
    
    /**
     * Request
     * 
     * @var Http\Request|null
     */
    protected $_request = null;
    
    /**
     * Application
     * 
     * @var App
     */
    protected $_app = null;

    /**
     * Response object 
     * 
     * @var \Magelight\Http\Response
     */
    protected $_response = null;

    /**
     * Current route action
     *
     * @var array
     */
    protected $_routeAction = [];
    
    /**
     * View object
     * 
     * @var \Magelight\Block
     */
    protected $_view = null;

    /**
     * Constructor
     * 
     * @param Http\Request $request
     * @param array $routeAction
     *
     */
    public function init(\Magelight\Http\Request $request = null, array $routeAction = [])
    {
        $this->_request = ($request instanceof \Magelight\Http\Request)
            ? $request :\Magelight\Http\Request::getInstance();
        $this->_routeAction = $routeAction;
        $this->_app = \Magelight::app();
        $this->_response = \Magelight\Http\Response::forge();
    }

    /**
     * Set view object or class name
     * 
     * @param \Magelight\Block|string $view
     *
     * @return Controller
     */
    protected function setView($view)
    {
        $this->_view = $view;
        return $this;
    }
        
    /**
     * Get app
     * 
     * @return App|null
     */
    public function app()
    {
        return $this->_app;
    }
    
    /**
     * Get request
     * 
     * @return Http\Request|null
     */
    protected function request()
    {
        return $this->_request;
    }

    /**
     * Get response object
     * 
     * @return Http\Response|null
     */
    protected function response()
    {
        return $this->_response;
    }

    /**
     * Get view object
     * 
     * @return \Magelight\Block|null
     */
    protected function view()
    {
        if (!$this->_view instanceof \Magelight\Block && is_string($this->_view)) {
            $this->_view = call_user_func([$this->_view, 'forge']);
        }
        return $this->_view;
    }

    /**
     * Get server srapper object
     *
     * @return \Magelight\Http\Server
     */
    protected function server()
    {
        return \Magelight\Http\Server::getInstance();
    }

    /**
     * Render view
     * 
     * @return Controller
     */
    protected function renderView()
    {
        $this->response()->setContent($this->view()->toHtml())->send();
        return $this;
    }
    
    /**
     * Before execution
     * 
     * @return \Magelight\Controller
     */
    public function beforeExecute()
    {
        return $this;
    }
    
    /**
     * After execution
     * 
     * @return \Magelight\Controller
     */
    public function afterExecute()
    {
        return $this;
    }

    /**
     * Forward action
     *
     * @param string $action
     *
     * @return mixed
     * @throws \Magelight\Exception
     */
    public function forward($action)
    {
        $action = $action . 'Action';
        if ($this->_app->isInDeveloperMode() && !is_callable([$this, $action])) {
            $controller = get_class($this);
            throw new \Magelight\Exception("Forwarding to undefined controller action {$action} in {$controller}");
        }
        return $this->$action();
    }

    /**
     * Fetch url by match mask
     *
     * @param string $match - url match mask
     * @param array $params - params to be passed to URL
     * @param string $type - URL type (http|https)
     * @return string
     */
    public function url($match, $params = [], $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP)
    {
        return \Magelight\Helpers\UrlHelper::getInstance()->getUrl($match, $params, $type);
    }

    /**
     * Redirect to url
     *
     * @param string $url
     */
    public function redirect($url)
    {
        $this->server()->sendHeader("Location: $url");
        \Magelight::app()->shutdown();
    }

    /**
     * Redirect to internal url
     *
     * @param string $url
     * @param array $params
     */
    public function redirectInternal($url, $params = [], $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP)
    {
        $this->redirect($this->url($url, $params, $type));
    }

    /**
     * Get session object
     *
     * @return Http\Session
     */
    public function session()
    {
        return \Magelight\Http\Session::getInstance();
    }

    /**
     * Forward and dispatch controller action with current request
     *
     * @param string $controller
     * @param string $action
     * @return App
     */
    public function forwardController($controller, $action)
    {
        $module = $this->getCurrentModuleName();
        return $this->app()->dispatchAction(
            [
                'module' => $module,
                'controller' => $controller,
                'action' => $action
            ],
            $this->request()
        );
    }

    /**
     * Generate security action token and save it to session
     *
     * @param string $index
     *
     * @return Controller
     */
    public function generateToken($index = self::DEFAULT_TOKEN_SESSION_INDEX)
    {
        $this->session()->set($index, md5(rand(0,999999999)));
        return $this;
    }

    /**
     * Check is token valid
     *
     * @param string $token
     * @param string $index
     * @return bool
     */
    public function checkToken($token, $index = self::DEFAULT_TOKEN_SESSION_INDEX)
    {
        return $this->getToken($index) === $token;
    }

    /**
     * Get current token
     *
     * @param string $index
     * @return mixed
     */
    public function getToken($index = self::DEFAULT_TOKEN_SESSION_INDEX)
    {
        return $this->session()->get($index, null);
    }

    /**
     * Silent action execution
     *
     * @param string $action
     * @return string
     */
    public function silent($action)
    {
        ob_start();
        $this->$action;
        return ob_get_clean();
    }

    /**
     * Set lock for current controller action (REQUIRES CACHE)
     *
     * @param int $ttl
     * @param string $cacheIndex
     * @return bool
     */
    public function lockCurrentAction($ttl = 60, $cacheIndex = \Magelight\App::DEFAULT_INDEX)
    {
        if ($this->app()->cache($cacheIndex)->setNx($this->_getLockKey(), 1, $ttl)) {
            return true;
        }
        return false;
    }

    /**
     * Unlock controller action
     *
     * @param string $cacheIndex
     * @return bool
     */
    public function unlockCurrentAction($cacheIndex = \Magelight\App::DEFAULT_INDEX)
    {

        if ($this->app()->cache($cacheIndex)->del($this->_getLockKey())) {
            return true;
        }
        return false;
    }

    /**
     * Get action lock key
     *
     * @return string
     */
    protected function _getLockKey()
    {
        ksort($this->_routeAction);
        return md5(serialize($this->_routeAction) . '_lock');
    }
}
