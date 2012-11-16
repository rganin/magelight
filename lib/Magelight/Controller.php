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

abstract class Controller
{
    use \Magelight\Forgery;

    /**
     * Default controller action
     */
    const DEFAULT_ACTION = 'index';
    
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
     * View object
     * 
     * @var \Magelight\Block|null
     */
    protected $_view = null;

    /**
     * Constructor
     * 
     * @param Http\Request $request
     */
    public function init(\Magelight\Http\Request $request)
    {
        $this->_request = $request;
        $this->_app = \Magelight::app();
        $this->_response = new \Magelight\Http\Response();
    }

    /**
     * Get object from application registry
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getRegistryObject($key, $default = null)
    {
        return $this->app()->getRegistryObject($key, $default);
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
}
