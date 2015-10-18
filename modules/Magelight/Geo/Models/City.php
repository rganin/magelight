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

namespace Magelight\Geo\Models;

/**
 * @method static \Magelight\Geo\Models\City find($id)
 * @method static \Magelight\Geo\Models\City findBy($field, $value)
 * @method static \Magelight\Geo\Models\City forge($data = [], $forceNew = false)
 */
class City extends \Magelight\Model
{
    protected static $tableName = 'geo_cities';

    public function getCityIdByName($name, $langs = ['en', 'ru', 'ua'])
    {
        $orm = self::orm()->selectFields([self::$idField]);
        foreach ($langs as $lang) {
            $orm->orWhereLike('city_name_' . $lang, $name);
        }
        return $orm->fetchFirstColumnElement();
    }
}
