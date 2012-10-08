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
 * @version   $$version_placeholder_notice$$
 * @uthor     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Forgery;

abstract class SingletonForgery extends Forgery
{
    /**
     * Protected cloner
     */
    protected function __clone()
    {
        
    }

    /**
     * Protected unserializer
     */
    protected function __wakeup()
    {
        
    }

    /**
     * Protected serializer
     * 
     * @throws \Magelight\Exception
     */
    protected function __sleep()
    {
        throw new \Magelight\Exception('Can not serialize SingletonForgery instance');
    }

    /**
     * Get instance of class
     * 
     * @return SingletonForgery
     */
    public static function getInstance()
    {
        static $instance;
        $className = static::getClassName();
        if (!$instance instanceof $className) {
            $instance = static::forge();
        }
        return $instance;
    }

    /**
     * Get current module name
     *
     * @return string|null
     */
    protected function _getCurrentModuleName()
    {
        $namespace = explode('\\', get_called_class());
        if (!empty($namespace[0])) {
            return $namespace[0];
        }
        return null;
    }
}
