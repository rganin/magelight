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

abstract class Controller extends \Bike\Prototypes\Overridable
{
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
     * @var \Bike\Http\Response
     */
    protected $_response = null;
    
    /**
     * View object
     * 
     * @var \Bike\Block|null
     */
    protected $_view = 'Bike\\Html\\Document';
    
    /**
     * Constructor
     * 
     * @param Http\Request $request
     */
    public function init(\Bike\Http\Request $request)
    {
        $this->_request = $request;
        $this->_app = \Bike::app();
        $this->_response = new \Bike\Http\Response();
    }

    /**
     * Set view object or class name
     * 
     * @param Block|string $view
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
     * @return \Bike\Block|null
     */
    protected function view()
    {
        if (!$this->_view instanceof \Bike\Block && is_string($this->_view)) {
            $this->_view = call_user_func(array($this->_view, 'create'));
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
     * @return \Bike\Controller
     */
    public function beforeExecute()
    {
        return $this;
    }
    
    /**
     * After execution
     * 
     * @return \Bike\Controller
     */
    public function afterExecute()
    {
        return $this;
    }
}