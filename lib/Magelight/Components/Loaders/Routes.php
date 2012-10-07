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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components\Loaders;

class Routes
{
    /**
     * Regular expression for routes matching
     */
    const MATCH_REGEX = "/\{(?P<name>([a-z0-9\-_]+))(\:(?P<regex>(.*)))*\}/iU"; 
    
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
    private $_routes = array();

    /**
     * Routes back index by module/controller/action
     * 
     * @var array
     */
    private $_routesBackIndex = array();
    
    /**
     * Parse routes from file
     * 
     * @param $file
     * @return Routes
     */
    public function parseRoutes($file)
    {
        $xml = simplexml_load_file($file);
        return $this->parseXmlModules($xml);
    }
    
    /**
     * Get loaded routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
    
    /**
     * Parse modules from xml
     * 
     * @param \SimpleXMLElement $xmlObject
     * @return Routes
     */
    public function parseXmlModules(\SimpleXMLElement $xmlObject)
    {
        foreach ($xmlObject->children() as $module) {
            $this->parseModuleRoutes($module);
        }
        return $this;
    }
    
    /**
     * Parse module routes from xml
     * 
     * @param \SimpleXMLElement $moduleRoutesXml
     * @return Routes
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
     * Parse routes XML recursively
     * 
     * @param \SimpleXMLElement $routeXml
     * @param string $moduleName
     * @param null $parentRoute
     * @return Routes
     * @throws \Magelight\Exception
     */
    public function parseRoute(\SimpleXMLElement $routeXml, $moduleName, $parentRoute = null)
    {
        if ($routeXml->getName() === 'route') {
            $route['module'] =  $moduleName = !empty($routeXml->module) ? (string) $routeXml->module : $moduleName;
            $route['match'] = (isset($parentRoute['match']) ? $parentRoute['match'] : '' )
                . '/' 
                . ltrim($routeXml->attributes()->match, '/');
            
            if (is_null($route['match'])) {
                throw new \Magelight\Exception('Route without match in module ' . $moduleName);
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
                    $this->_routes[$routeKey] = $route;
                }
            }
            
            foreach ($routeXml->children() as $childRouteXml) {
                $this->parseRoute($childRouteXml, $moduleName, $route);
            }
        }
        return $this;
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
     * @throws \Magelight\Exception
     */
    public function canOverrideRoute($moduleName, $match, $rank)
    {
        if (isset($this->_routes[$match]['rank'])) {
            if ($this->_routes[$match]['rank'] === $rank) {
                throw new \Magelight\Exception(
                    'Routes with same match (' 
                    . $match 
                    . ') and rank (' 
                    . $rank 
                    . ') in modules ' 
                    . $moduleName 
                    . ' and ' 
                    . $this->_routes[$match]['module']
                );    
            } elseif ($this->_routes[$match]['rank'] > $rank) {
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
        if (preg_match_all(self::MATCH_REGEX, $matchKey, $matches, PREG_SET_ORDER)) {
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
    
    public function buildRoutesBackIndex()
    {
        foreach ($this->_routes as $route) {
            $this->_routesBackIndex[$route['module']][$route['controller']][$route['action']] = $route['match'];
        }
        return $this;
    }
    
    public function getRoutesBackIndex()
    {
        return $this->_routesBackIndex;
    }
}