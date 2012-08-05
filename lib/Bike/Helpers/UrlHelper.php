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

namespace Bike\Helpers;

/**
 * Url helper class
 * @static getInstance \Bike\Helpers\UrlHelper 
 */
class UrlHelper extends \Bike\Prototypes\Singleton
{
    /**
     * Url types
     */
    const TYPE_HTTP = 'http';
    const TYPE_HTTPS = 'https';
    
    protected $_app = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_app = \Bike::app();
    }

    /**
     * Get bike base URL
     * 
     * @param string $type
     * 
     * @return string
     */
    public function getBaseUrl($type = self::TYPE_HTTP)
    {
        $domain = $this->_app->getConfig('global/base_domain', null);
        if (is_null($domain)) {
            $server = \Bike\Http\Server::getInstance();
            /* @var \Bike\Http\Server $server*/
            $domain = $server->getCurrentDomain();
        }
        return $type . '://' . $domain;
    }

    /**
     * Get plain url
     * 
     * @param        $path
     * @param string $type
     *
     * @return string
     */
    public function getPlainUrl($path, $type = self::TYPE_HTTP) 
    {
        return $this->getBaseUrl($type) . '/' . $path; 
    }

    /**
     * Get url by controller/action
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array  $params
     * @param string $type
     *
     * @return string
     * @throws \Bike\Exception
     */
    public function getUrl($module, 
                           $controller, 
                           $action = \Bike\Controller::DEFAULT_ACTION, 
                           $params = array(),
                           $type = self::TYPE_HTTP
    )
    {
        $routes = \Bike::app()->router()->getRoutesBackIndex();

        if (\Bike::app()->isInDeveloperMode() && !isset($routes[ucfirst($module)][$controller][$action])) {
            throw new \Bike\Exception("No route for module={$module}/controller={$controller}/action={$action}!", 
                E_USER_NOTICE
            );
        }
                
        $url = $routes[ucfirst($module)][$controller][$action];
        
        if (\Bike::app()->isInDeveloperMode() && !$this->checkParamsWithPlaceholderMask($url, $params)) {
            throw new \Bike\Exception("Passed url params don`t match route mask.", E_USER_NOTICE);
        }
                
        if (!empty($params)) {
            $url = $this->setParamsToPlaceholders($url, $params);
        }
        
        return $this->getBaseUrl($type) . $url;
    }

    /**
     * Check Url params by mask
     * 
     * @param $match
     * @param $params
     *
     * @return bool
     */
    protected function checkParamsWithPlaceholderMask($match, $params) 
    {
        if (preg_match_all( \Bike\Loaders\Routes::MATCH_REGEX, $match, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $name = $match['name'];
                $mask = isset($match['regex']) ? $match['regex'] : \Bike\Loaders\Routes::DEFAULT_REGEX;
                if (isset($params[$name])) {
                    if (!preg_match("/^([{$mask}]*)$/", $params[$name])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Set url params to match placeholders
     * 
     * @param       $match
     * @param array $params
     *
     * @return mixed
     */
    protected function setParamsToPlaceholders($match, &$params = array()) 
    {
        foreach ($params as $key => $value) {
            $match = preg_replace("/(\{{$key}\}|\{{$key}:[^\}]*\})/", $value, $match);
            unset($params[$key]);
        }
        $match = preg_replace("/(\{[^\}]*\})/", '', $match); //cleaning not used placeholders
        return $match;
    }
}