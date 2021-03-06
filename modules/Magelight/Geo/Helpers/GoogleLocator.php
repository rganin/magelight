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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Geo\Helpers;

/**
 * Google locator helper
 *
 * @method static  $this forge()
 */
class GoogleLocator
{
    use \Magelight\Traits\TForgery;

    /**
     * Get route for addresses as json string
     *
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $lang
     * @param array $waypoints
     * @return null|string
     */
    public function getRoute($fromAddress, $toAddress, $lang = 'en', $waypoints = [])
    {
        $uri = 'http://maps.googleapis.com/maps/api/directions/json';
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: {$lang}\r\n",
            )
        );
        foreach ($waypoints as $key => $waypoint) {
            $waypoints[$key] = str_replace('|', '', $waypoint);
        }
        $params = [
            'origin' => $fromAddress,
            'destination' => $toAddress,
            'region' => $lang,
            'sensor' => 'false',
            'waypoints' => implode('|', $waypoints)
        ];
        $uri .= '?' . http_build_query($params);
        $context = stream_context_create($opts);
        $route = @file_get_contents($uri, false, $context);

        return !empty($route) ? $route : null;
    }

    /**
     * get route as array
     *
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $lang
     * @param array $waypoints
     * @return mixed|array
     */
    public function getRouteAsArray($fromAddress, $toAddress, $lang = 'en', $waypoints = [])
    {
        return json_decode($this->getRoute($fromAddress, $toAddress, $lang, $waypoints), true);
    }

    /**
     * Get route as object
     *
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $lang
     * @param array $waypoints
     * @return mixed|array
     */
    public function getRouteAsObject($fromAddress, $toAddress, $lang = 'en', $waypoints = [])
    {
        return json_decode($this->getRoute($fromAddress, $toAddress, $lang, $waypoints), false);
    }
}
