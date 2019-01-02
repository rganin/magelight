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

namespace Magelight\Traits;

/**
 * Forgery trait
 */
trait TForgery
{
    /**
     * Mock objects stack
     *
     * @var array
     */
    protected static $mockObjects = [];

    /**
     * Forgery calls by class
     *
     * @var array
     */
    protected static $calls = [];

    /**
     * Get Forgery instance
     *
     * @return \Magelight\Forgery
     */
    public static function getForgery()
    {
        return \Magelight\Forgery::getInstance();
    }

    /**
     * Force forgery to mock objects on iteration
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $mockObject
     * @param null $iteration
     */
    public static function forgeMock(\PHPUnit_Framework_MockObject_MockObject $mockObject, $iteration = null)
    {
        $className = get_called_class();
        \Magelight\Forgery\MockContainer::getInstance()->addMockObject($className, $mockObject, $iteration);
    }

    /**
     * Forge object
     *
     * @return mixed
     */
    public static function forge()
    {
        $className = get_called_class();
        $className = self::getForgery()->getClassName($className);
        $object = \Magelight\Forgery\MockContainer::getInstance()->getMock($className);
        if ($object instanceof $className) {
            return $object;
        }
        if (!self::_checkInterfaces($className)) {
            trigger_error(
                "Forgery error: Class {$className} must implement all interfaces described in it`s override container!",
                E_USER_ERROR
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
        $requiredInterfaces = self::getForgery()->getClassInterfaces($className);
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
        $className = get_called_class();
        $object = \Magelight\Forgery\MockContainer::getInstance()->getMock($className);
        if ($object instanceof $className) {
            return $object;
        }
        if (!$instance instanceof $className) {
            $instance = call_user_func_array([$className, 'forge'], func_get_args());
        }
        return $instance;
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
        return call_user_func_array([get_called_class(), $method], $arguments);
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
            list ($class, $method) = [$method[0], $method[1]];
        } else {
            list ($class, $method) = [get_called_class(), $method];
        }
        return call_user_func_array([$class, $method], $arguments);
    }

    /**
     * Get class redefinition name
     *
     * @return mixed
     */
    public static function getClassRedefinition()
    {
        return self::getForgery()->getClassName(get_called_class());
    }
}
