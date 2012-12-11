<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 18:30
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Blocks\Order;

class OrderList extends \Magelight\Block
{
    protected $_template = 'Cargo/templates/order/list.phtml';

    public function formatTime($value)
    {
        $time = $this->secondsToTime($value);
        return ($time['h'] ? "{$time['h']} ч " : '' ) . "{$time['m']} мин";
    }

    protected function secondsToTime($seconds)
    {
        $hours = floor($seconds / (60 * 60));
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
        $obj = [
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        ];
        return $obj;
    }
}