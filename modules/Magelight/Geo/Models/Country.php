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
