<?php
/**
 * $$name_placeholder_notice$$
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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Prototypes;

abstract class Overridable
{
    /**
     * Classes overrides
     * 
     * @var array
     */
    protected static $_classOverrides = array();

    /**
     * Just closing constructor, be careful to open
     */
    protected function __construct()
    {
        
    }

    /**
     * Create object with overridable class
     * 
     * @static
     * @return mixed
     */
    public static function create()
    {
        $className = static::getClassName();
        return new $className();
    }

    /**
     * Get class name with overrides
     * 
     * @static
     * @return string
     */
    final protected static function getClassName()
    {
        $className = get_called_class();
        while (!empty(self::$_classOverrides[$className])) {
            $className = self::$_classOverrides[$className];
        }
        return $className;
    }
    
    /**
     * Add class to override
     * 
     * @static
     * @param $sourceClassName
     * @param $replacementClassName
     */
    final public static function addClassOverride($sourceClassName, $replacementClassName)
    {
        self::$_classOverrides[$sourceClassName] = $replacementClassName;
    }
}