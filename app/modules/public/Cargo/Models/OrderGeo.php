<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 23:20
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Models;

/**
 *
 * @method static  \Cargo\Models\OrderGeo forge($data = [], $forceNew = false)
 *
 * @property int $order_id
 * @property float $latitude_from
 * @property float $longitude_from
 * @property float $latitude_to
 * @property float $longitude_to
 * @property string $geo_address_from
 * @property string $geo_address_to
 * @property float $route_length
 * @property float $route_time
 * @property int $city_from_id
 * @property int $city_to_id
 * @property string $route_google_response
 */
class OrderGeo extends \Magelight\Model
{
    /**
     * Table name for model
     *
     * @var string
     */
    protected static $_tableName = 'order_geo';

    /**
     * Model ID fields (emulating)
     *
     * @var string
     */
    protected static $_idField = 'order_id';

    /**
     * Create geo information set for order
     *
     * @param $orderId
     * @return bool|OrderGeo
     */
    public function createForOrder($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return false;
        }
        $city = \Magelight\Geo\Models\City::forge();

        $this->city_from_id = $city->getCityIdByName($order->city_from);
        $this->city_to_id = $city->getCityIdByName($order->city_to);
        $this->order_id = $orderId;

        $addressFrom = $order->city_from . ',' . $order->address_from;
        $addressTo = $order->city_to . ',' . $order->address_to;
        $route = \Magelight\Geo\Helpers\GoogleLocator::forge()->getRoute($addressFrom, $addressTo, 'ru');

        $this->route_google_response = $route;
        $route = json_decode($route, true);

        if (isset($route['routes'][0]['legs'][0])) {
            $route = $route['routes'][0]['legs'][0];
        }

        $this->latitude_from = isset($route['start_location']['lat'])
                                   ? $route['start_location']['lat'] : 0;

        $this->longitude_from = isset($route['start_location']['lng'])
                                    ? $route['start_location']['lng'] : 0;

        $this->latitude_to = isset($route['end_location']['lat'])
                                 ? $route['end_location']['lat'] : 0;

        $this->longitude_to = isset($route['end_location']['lng'])
                                  ? $route['end_location']['lng'] : 0;

        $this->geo_address_from = isset($route['start_address'])
                                      ? $route['start_address'] : $addressFrom;

        $this->geo_address_to = isset($route['end_address'])
                                    ? $route['end_address'] : $addressTo;

        $this->route_length = isset($route['distance']['value'])
                                  ? $route['distance']['value'] : 0;

        $this->route_time = isset($route['duration']['value'])
                                  ? $route['duration']['value'] : 0;
        return $this;
    }
}