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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Helpers;

/**
 * Array helper
 * @method static $this forge()
 */
class ArrayHelper
{
    use \Magelight\Traits\TForgery;

    /**
     * Insert to array
     * 
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @param string $after
     * @return array
     */
    public function insertToArray($array, $key, $value, $after = null)
    {
        if (empty($after)) {
            $array[$key] = $value; 
            return $array;
        }
        $tmp = array();
        foreach (array_keys($array) as $pkey) {
            $tmp[$pkey] = $array[$pkey];  
            if ($pkey === $after) {
                $tmp[$key] = $value;
            }
        }
        $array = $tmp;
        return $array;
    }

    /**
     * Flattern nested array into HTML form-like structure
     *
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public function flatternArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value)
        {
            $newKey = $prefix . (empty($prefix) ? $key : '[' . $key . ']');
            if (is_array($value)) {
                $result = array_merge($result, $this->flatternArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }
}
