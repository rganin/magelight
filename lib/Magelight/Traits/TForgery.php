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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Traits;

/**
 * Forgery trait
 */
trait TForgery
{
    /**
     * Get Forgery instance
     *
     * @return \Magelight\Forgery
     */
    protected static function _getForgery()
    {
        return \Magelight\Forgery::getInstance();
    }

    /**
     * Forge object
     *
     * @return mixed
     * @throws \Magelight\Exception
     */
    public static function forge()
    {
        $className = self::_getForgery()->getClassName(get_called_class());
        if (!self::_checkInterfaces($className)) {
            throw new \Magelight\Exception(
                "Forgery error: Class {$className} must implement all interfaces described in it`s override container!"
            );
        }
        $object = new $className;
        if (method_exists($object, '__forge')) {
            $arguments = func_get_args();
            call_user_func_array([$object, '__forge'], $arguments);
        }
        return $object;
    }

    /**
     * Check class interfaces (checks that class is derived from one of overriden ones)
     *
     * @param string $className
     * @return bool
     */
    final static protected function _checkInterfaces($className)
    {
        $requiredInterfaces = self::_getForgery()->getClassInterfaces($className);
        $implementedInterfaces = class_implements($className, true);
        foreach ($requiredInterfaces as $interface) {
            if (!isset($implementedInterfaces[$interface])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Forge singleton instance
     *
     * @return Object
     */
    public static function getInstance()
    {
        static $instance;
        $className = self::_getForgery()->getClassName(get_called_class());

        if (!$instance instanceof $className) {
            $instance = new $className();
            if (method_exists($instance, '__forge')) {
                call_user_func_array([$instance, '__forge'], func_get_args());
            }
        }

        return $instance;
    }

    /**
     * Get class redefinition name
     *
     * @return mixed
     */
    public static function getClassRedefinition()
    {
        return self::_getForgery()->getClassName(get_called_class());
    }

    /**
     * Get current module name
     *
     * @return string|null
     */
    public static function getCurrentModuleName()
    {
        $namespace = explode('\\', get_called_class());
        if (!empty($namespace[0])) {
            return $namespace[0];
        }
        return null;
    }

    /**
     * Get current module name
     *
     * @return string|null
     */
    public static function getRedefinitionModuleName()
    {
        $namespace = explode('\\', self::_getForgery()->getClassName(get_called_class()));
        if (!empty($namespace[0])) {
            return $namespace[0];
        }
        return null;
    }

    /**
     * Call public static method from class redefinition
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function callStatic($method, $arguments = [])
    {
        return call_user_func_array([self::getClassRedefinition(), $method], $arguments);
    }

    /**
     * Call public late static method from class redefinition
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function callStaticLate($method, $arguments = [])
    {
        if (is_array($method) && count($method) > 1) {
            list ($class, $method) = [self::_getForgery()->getClassName($method[0]), $method[1]];
        } else {
            list ($class, $method) = [static::getClassRedefinition(), $method];
        }
        return call_user_func_array([$class, $method], $arguments);
    }
}
