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
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


namespace Http;

class Request
{
    /**
     * Default request merging order
     */
    const DEFAULT_REQUEST_MERGE_ORDER = 'GP';
    
    /**
     * GET params
     * 
     * @var array|null
     */
    private $_get;
    
    /**
     * POST params
     * 
     * @var array|null
     */
    private $_post;
    
    /**
     * REQUEST params
     * 
     * @var array|null
     */
    private $_request;
    
    /**
     * Constructor
     * 
     * @param array $get
     * @param array $post
     * @param string $requestOrder
     */
    public function __construct($get = array(), $post = array(), $requestOrder = self::DEFAULT_REQUEST_MERGE_ORDER)
    {
        if (empty($get)) {
            $this->_get = &$_GET;
        } else {
            $this->_get = $get;
        }
        
        if (empty($post)) {
            $this->_post = &$_POST;
        } else {
            $this->_post = $post;
        }
        
        if (empty($get) && empty($post) && ini_get('request_order') === self::DEFAULT_REQUEST_MERGE_ORDER) {
            $this->_request = &$_REQUEST;
        } else {
            $this->_request = $this->mergeRequest($get, $post);
        }
    }
    
    /**
     * Merge request var
     * 
     * @param array $get
     * @param array $post
     * @param string $order
     * @return array
     */
    protected function mergeRequest($get = array(), $post = array(), $order = self::DEFAULT_REQUEST_MERGE_ORDER)
    {
        if ($order == 'GP') {
            return array_merge($get, $post);
        }
        return array_merge($post, $get);
    }
    
    /**
     * Get REQUEST param by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getRequest($key, $default)
    {
        return isset($this->_request[$key]) ? $this->_request[$key] : $default;
    }
    
    /**
     * Get GET param by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGet($key, $default = null)
    {
        return isset($this->_get[$key]) ? $this->_get[$key] : $default;
    }
    
    /**
     * Get POST param by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost($key, $default = null)
    {
        return isset($this->_post[$key]) ? $this->_post[$key] : $default;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->_get = null;
        $this->_post = null;
        $this->_request = null;
        unset($this->_get);
        unset($this->_post);
        unset($this->_request);
    }
}
