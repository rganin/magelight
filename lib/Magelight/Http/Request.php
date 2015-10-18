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
    protected $method = 'GET';
    
    /**
     * GET params
     * 
     * @var array|null
     */
    protected $get;
    
    /**
     * POST params
     * 
     * @var array|null
     */
    protected $post;

    /**
     * FILES array
     *
     * @var array|null
     */
    protected $files;
    
    /**
     * REQUEST params
     * 
     * @var array|null
     */
    protected $request;

    /**
     * Cookies
     *
     * @var array
     */
    private $cookies = [];

    /**
     * Request path
     * 
     * @var string
     */
    protected $requestRoute = '/';

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
            $this->method = $_SERVER['REQUEST_METHOD'];
        }
        
        if (isset($_SERVER['REQUEST_URI'])) {
            $request = explode('?', $_SERVER['REQUEST_URI']);
            if (isset($request[0])) {
                $this->requestRoute = $request[0];
            }
        }
        
        if (empty($get)) {
            $this->get = $_GET;
        } else {
            $this->get = $get;
        }
        
        if (empty($post)) {
            $this->post = $_POST;
        } else {
            $this->post = $post;
        }

        if (empty($files)) {
            $this->files = $_FILES;
        } else {
            $this->files = $files;
        }

        $this->cookies = $_COOKIE;
        
        if (empty($get) && empty($post) && ini_get('request_order') === self::DEFAULT_REQUEST_MERGE_ORDER) {
            $this->request = $_REQUEST;
        } else {
            $this->request = $this->mergeRequest($get, $post);
        }
    }
    
    /**
     * Get request route
     * 
     * @return string
     */
    public function getRequestRoute()
    {
        return $this->requestRoute;
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
        return isset($this->request[$key]) ? $this->request[$key] : $default;
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
        return $this->request;
    }

    /**
     * Get full POST array
     *
     * @return array|null
     */
    public function getPostArray()
    {
        return $this->post;
    }

    /**
     * Get full GET array
     *
     * @return array|null
     */
    public function getGetArray()
    {
        return $this->get;
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
        return isset($this->get[$key]) ? $this->get[$key] : $default;
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
        return isset($this->post[$key]) ? $this->post[$key] : $default;
    }
    
    /**
     * Append to get array
     * 
     * @param array $appendArray
     */
    public function appendGet($appendArray = [])
    {
        $this->get = array_merge($this->get, $appendArray);
        $this->request = array_merge($this->get, $this->post);
    }
    
    /**
     * Get request method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->get = null;
        $this->post = null;
        $this->request = null;
        unset($this->get);
        unset($this->post);
        unset($this->request);
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
            return $this->files;
        }
        return isset($this->files[$index]) ? $this->files[$index] : $default;
    }

    /**
     * Get whole files array
     *
     * @return array|null
     */
    public function getFilesArray()
    {
        return $this->files;
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
        $files = $this->rearrangeFilesArray($this->files);
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
        return $this->rearrangeFilesArray($this->files);
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
            return $this->cookies[$key];
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
        return isset($this->cookies[$key]);
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
