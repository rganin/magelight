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
     * Rendering perspective
     * 
     * @var null
     */
    protected $_perspective = null;
    
    /**
     * Constructor
     * 
     * @param Http\Request $request
     * @param \Bike\App $app
     */
    public function init(\Bike\Http\Request $request, \Bike\App $app)
    {
        $this->_request = $request;
        $this->_app = $app;
        $this->_response = new \Bike\Http\Response();
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
     * Before execution
     * 
     * @return \Bike\Controller
     */
    public function beforeExec()
    {
        return $this;
    }
    
    /**
     * After execution
     * 
     * @return \Bike\Controller
     */
    public function afterExec()
    {
        return $this;
    }
}