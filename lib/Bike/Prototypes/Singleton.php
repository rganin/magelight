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
 * @version   $$version_placeholder_notice$$
 * @author     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Prototypes;
/**
 * Singleton prototype
 */
abstract class Singleton
{
    /**
     * Closed constructor
     */
    protected function __construct()
    {
        
    }

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
     * @throws \Bike\Exception
     */
    protected function __sleep()
    {
        throw new \Bike\Exception('Can not serialize singleton');
    }

    /**
     * Get instance of class
     * 
     * @return Singleton
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static();
        }
        return $instance;
    }
}