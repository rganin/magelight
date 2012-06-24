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

namespace Bike;

class Router
{
    const DEFAULT_NOT_FOUND_ROUTE = '/404';
    
    /**
     * Default route
     */
    const DEFAULT_ROUTE = '/';
    
    /**
     * Default action
     */
    const DEFAULT_ACTION = 'index';
    
    /**
     * Default controller
     */
    const DEFAULT_CONTROLLER = 'index';
    
    /**
     * Default route rank
     */
    const DEFAULT_RANK = 0;
    
    /**
     * Default route var regexp
     */
    const DEFAULT_REGEX = 'a-zA-Z0-9_\-';

    /**
     * Routes
     * 
     * @var array
     */
    private $routes = array();
    
    /**
     * Parse routes
     * 
     * @param $file - path to xml file
     */
    public function parseRoutes($file)
    {
        $xml = simplexml_load_file($file);
        $this->parseXmlModules($xml);
//        var_dump($this->routes);
    }
    
    /**
     * Parse modules from xml
     * 
     * @param \SimpleXMLElement $xmlObject
     */
    public function parseXmlModules(\SimpleXMLElement $xmlObject)
    {
        foreach ($xmlObject->children() as $module) {
            $this->parseModuleRoutes($module);
        }
    }
    
    /**
     * Parse module routes from xml
     * 
     * @param \SimpleXMLElement $moduleRoutesXml
     * @return Router
     */
    public function parseModuleRoutes(\SimpleXMLElement $moduleRoutesXml)
    {
        $moduleName = $moduleRoutesXml->getName();
        foreach ($moduleRoutesXml->children() as $child) {
            $this->parseRoute($child, $moduleName);
        }
        return $this;
    }
    
    /**
     * Parse route from xml (recursively)
     * 
     * @param \SimpleXMLElement $routeXml
     * @param $moduleName
     * @param array $parentRoute
     * @throws Exception
     */
    public function parseRoute(\SimpleXMLElement $routeXml, $moduleName, $parentRoute = null)
    {
        if ($routeXml->getName() === 'route') {
            $route['module'] = $moduleName;
            $route['match'] = (isset($parentRoute['match']) ? $parentRoute['match'] : '' ) 
                . '/' 
                . $routeXml->attributes()->match;
            
            if (is_null($route['match'])) {
                throw new Exception('Route without match in module ' . $moduleName);
            } else {
                
                $rank = (isset($routeXml->attributes()->rank)) ? (int) $routeXml->attributes()->rank : self::DEFAULT_RANK;
                if ($this->canOverrideRoute($moduleName, $route['match'], $rank)) {
                    
                    $route['arguments'] = array();
                    $route['rank'] = $rank;
                    $route['regex'] = $this->isRegex($route['match']);
                    $route['headers'] = $this->getRouteHeaders($routeXml);
        
                    $routeKey = $route['regex'] ? $this->matchToRegex($route['match']) : $route['match'];
                    
                    if (is_null($routeXml->attributes()->controller)) {
                        $route['controller'] = 
                            isset($parentRoute['controller']) 
                                ? $parentRoute['controller'] 
                                : self::DEFAULT_CONTROLLER;
                    } else {
                        $route['controller'] = (string) $routeXml->attributes()->controller;
                    }
                    
                    if (is_null($routeXml->attributes()->action)) {
                        $route['action'] = 
                            isset($parentRoute['action']) 
                            ? $parentRoute['action'] 
                            : self::DEFAULT_ACTION;
                    } else {
                        $route['action'] = (string) $routeXml->attributes()->action;
                    }
                    $this->routes[$routeKey] = $route;
                }
            }
            
            foreach ($routeXml->children() as $childRouteXml) {
                $this->parseRoute($childRouteXml, $moduleName, $route);
            }
        }
    }
    
    /**
     * Get route headers
     * 
     * @param \SimpleXMLElement $routeXml
     * @return array
     */
    public function getRouteHeaders(\SimpleXMLElement $routeXml)
    {
        $headers = array();
        foreach ($routeXml->children() as $header) {
            /* @var $header \SimpleXMLElement*/
            if ($header->getName() === 'header') {
                $headers[] = (string) $header;
            }
        }
        return $headers;
    }
    
    /**
     * Check if route can override existing one
     * 
     * @param $moduleName
     * @param $match
     * @param $rank
     * @return bool
     * @throws Exception
     */
    public function canOverrideRoute($moduleName, $match, $rank)
    {
        if (isset($this->routes[$match]['rank'])) {
            if ($this->routes[$match]['rank'] === $rank) {
                throw new Exception(
                    'Routes with same match (' 
                    . $match 
                    . ') and rank (' 
                    . $rank 
                    . ') in modules ' 
                    . $moduleName 
                    . ' and ' 
                    . $this->routes[$match]['module']
                );    
            } elseif ($this->routes[$match]['rank'] > $rank) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Convert match to regular expression
     * 
     * @param $matchKey
     * @return mixed
     */
    public function matchToRegex($matchKey)
    {
        if (preg_match_all("/\{(?P<name>([a-z]+))(\:(?P<regex>(.*)))*\}/iU", $matchKey, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $name = $match['name'];
                $mask = isset($match['regex']) ? $match['regex'] : self::DEFAULT_REGEX;
                $matchKey = preg_replace("/(\{$name([^\}]*)\})/i", "(?P<$name>([$mask]*))", $matchKey);
            }
        }
        $matchKey = preg_replace("/(\/)/i", "\\/", $matchKey);
        $matchKey = '#^' . $matchKey . '[\/]*$#suU';
        return $matchKey;
    }
    
    /**
     * Is route requires a regex
     * 
     * @param $match
     * @return bool
     */
    public function isRegex($match)
    {
        return strpos($match, '{') !== false; // strict comparison required
    }
    
    /**
     * Get route action
     * 
     * @param $requestedRoute
     * @param string $notFound
     * @return array
     */
    public function getAction($requestedRoute, $notFound = self::DEFAULT_NOT_FOUND_ROUTE)
    {
        foreach ($this->routes as $match => $route) {
            if ($route['regex']) {
                if (preg_match($match, $requestedRoute, $arguments)) {
                    $keys = array_filter(array_keys($arguments), 'is_string');
                    $arguments = array_intersect_key($arguments, array_flip($keys));
                    $route['arguments'] = $arguments;
                    return $route;
                }
            } else {
                if (strcmp($match, $requestedRoute) === 0) {
                    return $route;
                }
            }
        }
        return $this->routes[$notFound];
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->routes);
    }
}