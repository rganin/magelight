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
 * @method static \Magelight\Geo\Models\Region find($id)
 * @method static \Magelight\Geo\Models\Region findBy($field, $value)
 * @method static \Magelight\Geo\Models\Region forge($data = [], $forceNew = false)
 */
class Region extends \Magelight\Model
{
    protected static $_tableName = 'geo_regions';

    /**
     * Get region cities
     *
     * @param string $langSuffix - language suffix
     * @return array
     */
    public function getCities($langSuffix = 'en')
    {
        return City::orm()->selectFields(['id', 'city_name_' . $langSuffix])
            ->whereEq('region_id', $this->id)->fetchAll(true);
    }
}
