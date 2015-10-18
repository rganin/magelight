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
 * @method static \Magelight\Geo\Models\Region find($id)
 * @method static \Magelight\Geo\Models\Region findBy($field, $value)
 * @method static \Magelight\Geo\Models\Region forge($data = [], $forceNew = false)
 */
class Region extends \Magelight\Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $tableName = 'geo_regions';

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
