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

namespace Bike\Components;

class Router
{
    /**
     * Default route if not found
     */
    const DEFAULT_NOT_FOUND_ROUTE = '/404';
    
    /**
     * Router routes
     * 
     * @var array
     */
    protected $_routes = array();

    /**
     * Routes back index
     * 
     * @var array
     */
    protected $_routesBackIndex = array();
    
    protected $_app = null;
    
    public function __construct(\Bike\App $app)
    {
        $this->_app = $app;
        $loader = new \Bike\Components\Loaders\Routes();
        foreach (array_keys($app->modules()->getModules()) as $moduleName) {
            $filename = $app->getAppDir() . 'modules' . DS . $moduleName . DS . 'etc' . DS . 'routes.xml';
            if (file_exists($filename)) {
                $loader->parseRoutes($filename);
            }
        }
        $loader->buildRoutesBackIndex();
        $this->setRoutes($loader->getRoutes(), $loader->getRoutesBackIndex());
        unset($loader);
    }

    /**
     * Set router routes
     * 
     * @param array $routes
     * @param array $routesBackIndex
     */
    public function setRoutes($routes = array(), $routesBackIndex = array())
    {
        $this->_routes = $routes;
        $this->_routesBackIndex = $routesBackIndex;
    }
    
    /**
     * Get route action
     * 
     * @param string $requestedRoute
     * @param string $notFound
     * @return array
     */
    public function getAction($requestedRoute, $notFound = self::DEFAULT_NOT_FOUND_ROUTE)
    {
        foreach ($this->_routes as $match => $route) {
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
        return $this->_routes[$notFound];
    }

    /**
     * Get routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
    
    /**
     * Get routes back index
     * 
     * @return array
     */
    public function getRoutesBackIndex()
    {
        return $this->_routesBackIndex;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_routes);
    }
}