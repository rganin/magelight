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
 * @method static \Magelight\Geo\Models\Country find($id)
 * @method static \Magelight\Geo\Models\Country findBy($field, $value)
 * @method static \Magelight\Geo\Models\Country forge($data = [], $forceNew = false)
 * @property int $id
 * @property int $oid
 * @property string $country_name_ru
 * @property string $country_name_en
 * @property string $country_name_ua
 */
class Country extends \Magelight\Model
{
    /**
     * @var string
     */
    protected static $tableName = 'geo_countries';

    /**
     * Get counrty regions
     *
     * @param string $langSuffix - language
     * @param string $nameAlias - field name where region title will be written to
     * @return array
     */
    public function getRegions($langSuffix = 'en', $nameAlias = 'name')
    {
        return Region::orm()->selectFields(['id', 'region_name_' . $langSuffix . ' AS ' . $nameAlias])
            ->whereEq('country_id', $this->id)->fetchAll(true);
    }

    /**
     * Get country cities
     *
     * @param string $langSuffix - language suffix
     * @param string $nameAlias - field name where city title will be written to
     * @return array
     */
    public function getCities($langSuffix = 'en', $nameAlias = 'name')
    {
        return City::orm()->selectFields(['id', 'city_name_' . $langSuffix . ' AS ' . $nameAlias])
            ->whereEq('country_id', $this->id)->fetchAll(true);
    }

    /**
     * Get country ID by name
     *
     * @param $name
     * @param array $langs
     *
     * @return mixed
     */
    public function getCountryIdByName($name, $langs = ['en', 'ru', 'ua'])
    {
        $orm = self::orm()->selectFields([self::$idField]);
        foreach ($langs as $lang) {
            $orm->orWhereLike('country_name_' . $lang, $name);
        }
        return $orm->fetchFirstColumnElement();
    }
}
