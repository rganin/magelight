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

final class Bike
{
    /**
     * Application
     * 
     * @var \Bike\App
     */
    protected static $_app = null;
  
    /**
     * Overriding classes
     * 
     * @var array
     */
    protected static $_classesOverride = array();
    
    /**
     * @static
     * Get application object
     * 
     * @return \Bike\App|null
     */
    public static function app()
    {
        if (empty(self::$_app)) {
            self::$_app = new \Bike\App();
        }
        return self::$_app;
    }

    /**
     * Get helper by name
     * @static
     * 
     * @param $name
     *
     * @return mixed
     * @throws Bike\Exception
     */
    public static function helper($name)
    {
        $name = ucfirst($name);
        try {
            $helper = call_user_func(array('\\Bike\\Helpers\\' . $name . 'Helper', 'getInstance'));    
        } catch (Exception $e) {
            throw new \Bike\Exception($e->getMessage());
        }
        return $helper;
    }

    /**
     * Dump variable to STD or SOCKET output
     * 
     * @static
     *
     * @param $var
     */
    public static function dump($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    /**
     * Add class to override
     * 
     * @static
     * @param $sourceClassName
     * @param $replacementClassName
     */
    public static function addClassOverride($sourceClassName, $replacementClassName)
    {
        self::$_classesOverride[$sourceClassName] = $replacementClassName;
    }
    
    /**
     * Just autoloader
     * 
     * @static
     * @param $className
     */
    public static function autoload($className)
    {
        if(isset(self::$_classesOverride[$className])) {
//            require_once $className . '.php';
            $className = self::$_classesOverride[$className];
        }
        require_once $className . '.php';
    }
    
    /**
     * Create object by class name and constructor params
     * 
     * @param $class
     * @param array $constructorParams
     * @return mixed
     */
    public static function getObject($class, $constructorParams = array())
    {
        if(isset(self::$_classesOverride[$class])) {
            $class = self::$_classesOverride[$class];
        }
        if (!is_array($constructorParams)) {
            $constructorParams = array($constructorParams);
        }
        return new $class($constructorParams);
    }
    
    /**
     * Get singleton instance by class name and constructor params
     * 
     * @param $class
     * @param array $instantiateParams
     * @return mixed
     */
    public static function getSingleton($class, $instantiateParams = array()) 
    {
        if (!is_array($instantiateParams)) {
            $instantiateParams = array($instantiateParams);
        }
        return call_user_func_array(array($class, 'getInstance'), $instantiateParams);
    }
}