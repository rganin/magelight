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

namespace Magelight\Aop\Entity;

abstract class Advice
{
    /**
     * Called method reflection
     *
     * @var null|\ReflectionMethod
     */
    protected $_reflectionMethod = null;

    /**
     * Called method arguments
     *
     * @var array
     */
    protected $_reflectionArgs = [];

    /**
     * Called method context
     *
     * @var null
     */
    protected $_object = null;

    /**
     * Called method result
     *
     * @var null
     */
    protected $_result = null;

    /**
     * Cosntructor
     *
     * @param \ReflectionMethod $method
     * @param array $arguments
     * @param Object $object
     */
    public function __construct(\ReflectionMethod $method, array $arguments, $object = null)
    {
        $this->_reflectionMethod = $method;
        $this->_reflectionArgs = $arguments;
        $this->_object = $object;
    }

    /**
     * Execute wrapper
     *
     * @return mixed
     */
    public function execute()
    {
        $this->before();
        $this->_result = $this->_reflectionMethod->invokeArgs($this->_object, $this->_reflectionArgs);
        $this->after();
        return $this->_result;
    }

    /**
     * Before execution handler
     */
    public function before()
    {
    }

    /**
     * After execution handler
     */
    public function after()
    {
    }
}
