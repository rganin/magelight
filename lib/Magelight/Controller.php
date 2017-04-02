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

/**
 * Abstract application controller
 *
 * @method static $this forge()
 */
class Controller
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
    protected $request = null;
    
    /**
     * Application
     * 
     * @var App
     */
    protected $app = null;

    /**
     * Response object 
     * 
     * @var \Magelight\Http\Response
     */
    protected $response = null;

    /**
     * Current route action
     *
     * @var array
     */
    protected $routeAction = [];
    
    /**
     * View object
     * 
     * @var \Magelight\Block
     */
    protected $view = null;

    /**
     * Constructor
     * 
     * @param array $routeAction
     *
     */
    public function init(array $routeAction = [])
    {
        $this->request = \Magelight\Http\Request::getInstance();
        $this->routeAction = $routeAction;
        $this->app = \Magelight\App::getInstance();
        $this->response = \Magelight\Http\Response::forge();
    }

    /**
     * Set view object or class name
     * 
     * @param \Magelight\Block|string $view
     *
     * @return Controller
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }
        
    /**
     * Get app
     * 
     * @return App|null
     */
    public function app()
    {
        return $this->app;
    }
    
    /**
     * Get request
     * 
     * @return Http\Request|null
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get response object
     * 
     * @return Http\Response|null
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * Get view object
     * 
     * @return \Magelight\Block|null
     */
    public function view()
    {
        if (!$this->view instanceof \Magelight\Block && is_string($this->view)) {
            $this->view = call_user_func([$this->view, 'forge']);
        }
        return $this->view;
    }

    /**
     * Get server wrapper object
     *
     * @return \Magelight\Http\Server
     */
    public function server()
    {
        return \Magelight\Http\Server::getInstance();
    }

    /**
     * Render view
     * 
     * @return Controller
     */
    public function renderView()
    {
        $this->response()->setContent($this->view()->toHtml())->send();
        return $this;
    }
    
    /**
     * Before execution
     * 
     * @return \Magelight\Controller
     * @codeCoverageIgnore
     */
    public function beforeExecute()
    {
        return $this;
    }
    
    /**
     * After execution
     * 
     * @return \Magelight\Controller
     * @codeCoverageIgnore
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
        if (!is_callable([$this, $action])) {
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
    public function url($match, $params = [], $type = null)
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
        \Magelight\Http\Server::getInstance()->sendHeader("Location: $url");
        \Magelight\App::getInstance()->shutdown();
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
        $module = $this->app()->getCurrentAction()[0]['module'];
        return $this->app()->dispatchAction(
            [
                'module' => $module,
                'controller' => $controller,
                'action' => $action
            ]
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
        $this->session()->set($index, md5(rand(0, 999999999)));
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
        $this->$action();
        return ob_get_clean();
    }

    /**
     * Set lock for current controller action (REQUIRES CACHE)
     *
     * @param int $ttl
     * @return bool
     */
    public function lockCurrentAction($ttl = 60)
    {
        if (\Magelight\Cache\AdapterPool::getInstance()->getAdapter()->setNx($this->getLockKey(), 1, $ttl)) {
            return true;
        }
        return false;
    }

    /**
     * Unlock controller action
     *
     * @return bool
     */
    public function unlockCurrentAction()
    {

        if (\Magelight\Cache\AdapterPool::getInstance()->getAdapter()->del($this->getLockKey())) {
            return true;
        }
        return false;
    }

    /**
     * Get action lock key
     *
     * @return string
     */
    protected function getLockKey()
    {
        ksort($this->routeAction);
        return md5(serialize($this->routeAction) . '_lock');
    }
}
