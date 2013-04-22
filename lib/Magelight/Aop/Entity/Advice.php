<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 21.04.13
 * Time: 13:35
 * To change this template use File | Settings | File Templates.
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