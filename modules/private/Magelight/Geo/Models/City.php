<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 14:54
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Geo\Models;

/**
 * @method static \Magelight\Geo\Models\City find($id)
 * @method static \Magelight\Geo\Models\City findBy($field, $value)
 * @method static \Magelight\Geo\Models\City forge($data = [], $forceNew = false)
 */
class City extends \Magelight\Model
{
    protected static $_tableName = 'geo_cities';

    public function getCityIdByName($name, $langs = ['en', 'ru', 'ua'])
    {
        $orm = self::orm()->selectFields([self::$_idField]);
        foreach ($langs as $lang) {
            $orm->orWhereLike('city_name_' . $lang, $name);
        }
        return $orm->fetchFirstColumnElement();
    }
}
