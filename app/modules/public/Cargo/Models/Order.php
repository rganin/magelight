<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 13:16
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Models;

class Order extends \Magelight\Model
{
    protected static $_tableName = 'orders';

    protected static $_defaultValues = [
        'weight' => 0,
        'max_price' => 0,
    ];

    public function _beforeSave()
    {
        $this->date_move = (int)strtotime($this->date_move);
        $this->date_added = time();
        $this->weight = floatval($this->weight);
        $this->max_price = floatval($this->max_price);
        $this->passengers = (int) $this->passengers;
        $this->loading_required = (int)isset($this->loading_required);
    }
}