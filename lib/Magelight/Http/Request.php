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

namespace Magelight\Http;

/**
 * Request wrapper
 *
 * @method static \Magelight\Http\Request getInstance($get = [], $post= [])
 */
class Request
{
    use \Magelight\Traits\TForgery;

    /**
     * Request methods
     */
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Default request merging order
     */
    const DEFAULT_REQUEST_MERGE_ORDER = 'GP';
    
    /**
     * Request method
     * 
     * @var string
     */
    protected $_method = 'GET';
    
    /**
     * GET params
     * 
     * @var array|null
     */
    protected $_get;
    
    /**
     * POST params
     * 
     * @var array|null
     */
    protected $_post;

    /**
     * FILES array
     *
     * @var array|null
     */
    protected $_files;
    
    /**
     * REQUEST params
     * 
     * @var array|null
     */
    protected $_request;

    /**
     * Cookies
     *
     * @var array
     */
    private $_cookies = [];

    /**
     * Request path
     * 
     * @var string
     */
    protected $_requestRoute = '/';

    /**
     * Forgery constructor
     *
     * @param array $get
     * @param array $post
     * @param array $files
     */
    public function __forge($get = [], $post = [], $files = [])
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->_method = $_SERVER['REQUEST_METHOD'];
        }
        
        if (isset($_SERVER['REQUEST_URI'])) {
            $request = explode('?', $_SERVER['REQUEST_URI']);
            if (isset($request[0])) {
                $this->_requestRoute = $request[0];
            }
        }
        
        if (empty($get)) {
            $this->_get = $_GET;
        } else {
            $this->_get = $get;
        }
        
        if (empty($post)) {
            $this->_post = $_POST;
        } else {
            $this->_post = $post;
        }

        if (empty($files)) {
            $this->_files = $_FILES;
        } else {
            $this->_files = $files;
        }

        $this->_cookies = $_COOKIE;
        
        if (empty($get) && empty($post) && ini_get('request_order') === self::DEFAULT_REQUEST_MERGE_ORDER) {
            $this->_request = $_REQUEST;
        } else {
            $this->_request = $this->mergeRequest($get, $post);
        }
    }
    
    /**
     * Get request route
     * 
     * @return string
     */
    public function getRequestRoute()
    {
        return $this->_requestRoute;    
    }
       
    /**
     * Merge request var
     * 
     * @param array $get
     * @param array $post
     * @param string $order
     * @return array
     */
    protected function mergeRequest($get = [], $post = [], $order = self::DEFAULT_REQUEST_MERGE_ORDER)
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
    public function getRequest($key, $default = null)
    {
        return isset($this->_request[$key]) ? $this->_request[$key] : $default;
    }

    /**
     * Get set of elements from request by array (vector) of keys
     *
     * @param array $keysArray
     * @param mixed $defaultElementValue
     * @return array|mixed
     */
    public function getRequestSet($keysArray, $defaultElementValue = null)
    {
        if (!is_array($keysArray)) {
            return $this->getRequest($keysArray, $defaultElementValue);
        }
        $ret = [];
        foreach ($keysArray as $requestIndex) {
            $ret[] = $this->getRequest($requestIndex, $defaultElementValue);
        }
        return $ret;
    }

    /**
     * Get full REQUEST array
     *
     * @return array|null
     */
    public function getRequestArray()
    {
        return $this->_request;
    }

    /**
     * Get full POST array
     *
     * @return array|null
     */
    public function getPostArray()
    {
        return $this->_post;
    }

    /**
     * Get full GET array
     *
     * @return array|null
     */
    public function getGetArray()
    {
        return $this->_get;
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
     * Append to get array
     * 
     * @param array $appendArray
     */
    public function appendGet($appendArray = [])
    {
        $this->_get = array_merge($this->_get, $appendArray);
        $this->_request = array_merge($this->_get, $this->_post);
    }
    
    /**
     * Get request method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
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

    /**
     * Get element of files array
     *
     * @param string $index
     * @param array $default
     * @return array|null
     */
    public function getFiles($index = null, $default = [])
    {
        if (empty($index)) {
            return $this->_files;
        }
        return isset($this->_files[$index]) ? $this->_files[$index] : $default;
    }

    /**
     * Get whole files array
     *
     * @return array|null
     */
    public function getFilesArray()
    {
        return $this->_files;
    }

    /**
     * Rearrange PHP FILES array
     *
     * @param array $files - $_FILES structured array
     * @return array
     */
    protected function rearrangeFilesArray($files)
    {
        $arrayForFill = [];
        foreach ($files as $firstNameKey => $arFileDescriptions) {
            foreach ($arFileDescriptions as $fileDescriptionParam => $mixedValue) {
                $this->restructFilesArray($arrayForFill,
                    $firstNameKey,
                    $files[$firstNameKey][$fileDescriptionParam],
                    $fileDescriptionParam
                );
            }
        }
        return $arrayForFill;
    }

    /**
     * Restructure PHP fucking $_FILES array
     *
     * @param array $arrayForFill
     * @param string $currentKey
     * @param mixed $currentMixedValue
     * @param string $fileDescriptionParam
     */
    protected function restructFilesArray(&$arrayForFill, $currentKey, $currentMixedValue, $fileDescriptionParam)
    {
        if (is_array($currentMixedValue)) {
            foreach ($currentMixedValue as $nameKey => $mixedValue) {
                $this->restructFilesArray($arrayForFill[$currentKey],
                    $nameKey,
                    $mixedValue,
                    $fileDescriptionParam
                );
            }
        } else {
            $arrayForFill[$currentKey][$fileDescriptionParam] = $currentMixedValue;
        }
    }
    /**
     * Get file info in normalized way
     *
     * @param string $index
     * @param array $default
     * @return array|FilesArray
     */
    public function getFilesNormalized($index = null, $default = [])
    {
        $files = $this->rearrangeFilesArray($this->_files);
        if (empty($index)) {
            return $files;
        }
        return isset($files[$index]) ? $files[$index] : $default;
    }

    /**
     * Get normalized files array
     *
     * @return FilesArray
     */
    public function getFilesArrayNormalized()
    {
        return $this->rearrangeFilesArray($this->_files);
    }

    /**
     * Get cookie by name
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($key, $default = null)
    {
        if ($this->cookieExists($key)) {
            return $this->_cookies[$key];
        }
        return $default;
    }

    /**
     * Check if cookie exists
     *
     * @param string $key
     * @return bool
     */
    public function cookieExists($key)
    {
        return isset($this->_cookies[$key]);
    }

    /**
     * Get cookie by name as object
     *
     * @param string $key
     * @return CookieObject
     */
    public function getCookieObject($key)
    {
        return CookieObject::forge($key, $this->getCookie($key));
    }
}
