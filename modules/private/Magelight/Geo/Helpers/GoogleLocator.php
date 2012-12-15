<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 23:29
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Geo\Helpers;

/**
 * Google locator helper
 *
 * @method static  \Magelight\Geo\Helpers\GoogleLocator forge()
 */
class GoogleLocator
{
    use \Magelight\Forgery;

    /**
     * Get route for addresses as json string
     *
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $lang
     * @return null|string
     */
    public function getRoute($fromAddress, $toAddress, $lang = 'en')
    {
        $uri = 'http://maps.googleapis.com/maps/api/directions/json';
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: {$lang}\r\n",
            )
        );
        $params = [
            'origin' => $fromAddress,
            'destination' => $toAddress,
            'region' => $lang,
            'sensor' => 'false',
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
     * @return mixed|array
     */
    public function getRouteAsArray($fromAddress, $toAddress, $lang = 'en')
    {
        return json_decode($this->getRoute($fromAddress, $toAddress, $lang), true);
    }

    /**
     * Get route as object
     *
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $lang
     * @return mixed|array
     */
    public function getRouteAsObject($fromAddress, $toAddress, $lang = 'en')
    {
        return json_decode($this->getRoute($fromAddress, $toAddress, $lang), false);
    }
}