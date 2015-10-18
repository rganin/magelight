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

namespace Magelight\Components\Loaders;
use Magelight\Traits\TForgery;

/**
 * Routes loader
 */
class Routes
{
    use TForgery;

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
    private $routes = [];

    /**
     * Load application routes
     *
     * @return Routes
     */
    public function loadRoutes()
    {
        $modules= \Magelight\Components\Modules::getInstance()->getActiveModules();
        foreach (array_reverse(\Magelight\App::getInstance()->getModuleDirectories()) as $modulesPath) {
            foreach ($modules as $module) {
                $filename = $modulesPath . DS . str_replace('/', DS, $module['path']) . DS . 'etc' . DS . 'routes.xml';
                if (file_exists($filename)) {
                    $xml = simplexml_load_file($filename);
                    $this->parseModuleRoutes($xml, $module['path']);
                }
            }
        }
        $this->routes = array_reverse($this->routes, true);
        return $this;
    }

    /**
     * Get loaded routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

   /**
    * Parse module routes from XML
    *
    * @param \SimpleXMLElement $moduleRoutesXml
    * @param string $moduleName
    *
    * @return Routes
    */
    public function parseModuleRoutes(\SimpleXMLElement $moduleRoutesXml, $moduleName)
    {
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
            $route['match'] = (isset($parentRoute['match']) ? rtrim($parentRoute['match'], '\\/') : '' )
                . '/' 
                . trim($routeXml->attributes()->match, '\\/');
            $route['module'] =  $moduleName = !empty($routeXml->attributes()->module)
                ? (string) $routeXml->attributes()->module
                : (isset($this->routes[$route['match']]) ? $this->routes[$route['match']]['module'] : $moduleName);
            if (is_null($route['match'])) {
                throw new \Magelight\Exception('Route without match in module ' . $moduleName);
            } else {
                $route['arguments'] = [];
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

    /**
     * Get routes match backindex
     *
     * @return array
     */
    public function getRoutesIndex()
    {
        $matchIndex = [];
        foreach ($this->routes as $route) {
            $matchIndex[$route['match']] = $route;
        }
        return $matchIndex;
    }
}
