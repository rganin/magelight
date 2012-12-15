<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 14:53
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Geo\Models;

/**
 * @method static \Magelight\Geo\Models\Country find($id)
 * @method static \Magelight\Geo\Models\Country findBy($field, $value)
 * @method static \Magelight\Geo\Models\Country forge($data = [], $forceNew = false)
 */
class Country extends \Magelight\Model
{
    protected static $_tableName = 'geo_countries';

    /**
     * Get counrty regions
     *
     * @param string $langSuffix - language
     * @return array
     */
    public function getRegions($langSuffix = 'en')
    {
        return Region::orm()->selectFields(['id', 'region_name_' . $langSuffix])
            ->whereEq('country_id', $this->id)->fetchAll(true);
    }

    /**
     * Get country cities
     *
     * @param string $langSuffix - language suffix
     * @return array
     */
    public function getCities($langSuffix = 'en')
    {
        return City::orm()->selectFields(['id', 'city_name_' . $langSuffix])
            ->whereEq('country_id', $this->id)->fetchAll(true);
    }

    public function getCountryIdByName($name, $langs = ['en', 'ru', 'ua'])
    {
        $orm = self::orm()->selectFields([self::$_idField]);
        foreach ($langs as $lang) {
            $orm->orWhereLike('country_name_' . $lang, $name);
        }
        return $orm->fetchFirstColumnElement();
    }
}
