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

namespace Magelight\Components;
use Magelight\Traits\TForgery;

/**
 * Application Router
 *
 * @method static Router getInstance(\Magelight\App $app)
 */
class Router
{
    use TForgery;

    /**
     * Default route if not found
     */
    const DEFAULT_NOT_FOUND_ROUTE = '/404';

    /**
     * Router routes
     * 
     * @var array
     */
    protected $routes = [];

    /**
     * Routes back index
     * 
     * @var array
     */
    protected $routesIndex = [];

    /**
     * Application instance
     *
     * @var \Magelight\App|null
     */
    protected $app = null;

    /**
     * Constructor
     *
     * @param \Magelight\App $app
     */
    public function __forge(\Magelight\App $app)
    {
        $this->app = $app;
        $loader = \Magelight\Components\Loaders\Routes::forge();
        //@todo add routes caching just as cache will be implemented
        $loader->loadRoutes();

        $this->routes = $loader->getRoutes();
        $this->routesIndex = $loader->getRoutesIndex();

        unset($loader);
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
        foreach ($this->routes as $match => $route) {
            if ($route['regex']) {
                if (preg_match($match, $requestedRoute, $arguments)) {
                    $keys = array_filter(array_keys($arguments), 'is_string');
                    $arguments = array_intersect_key($arguments, array_flip($keys));
                    $route['arguments'] = $arguments;
                    if (isset($arguments['action'])) {
                        $route['action'] = $arguments['action'];
                    }
                    if (isset($arguments['controller'])) {
                        $route['controller'] = $arguments['controller'];
                    }
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
     * Get routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    
    /**
     * Get routes back index
     * 
     * @return array
     */
    public function getRoutesIndex()
    {
        return $this->routesIndex;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->routes);
    }
}
