<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 04.04.13
 * Time: 13:14
 * To change this template use File | Settings | File Templates.
 */


namespace Magelight;

/**
 * Class ArrayWrapper
 * @package Magelight
 *
 * @method static \Magelight\ArrayWrapper forge($array, $defaultReturnValue = null)
 */
class ArrayWrapper
{
    use Traits\TForgery;

    /**
     * Default return value for getters
     *
     * @var mixed|null
     */
    protected $_defaultReturnValue = null;

    /**
     * Array to be wrapped
     *
     * @var array
     */
    protected $_array = [];

    /**
     * Forgery constructor
     *
     * @param array $array
     * @param null|mixed $defaultReturnValue
     */
    public function __forge($array, $defaultReturnValue = null)
    {
        $this->_array = $array;
        $this->_defaultReturnValue = $defaultReturnValue;
    }

    /**
     * Magic getter
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->_array[$name]) ? $this->_array[$name] : $this->_defaultReturnValue;
    }

    /**
     * Magic setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_array[$name] = $value;
    }

    /**
     * Get array data
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getData($name, $default)
    {
        return isset($this->_array[$name]) ? $this->_array[$name] : $default;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return ArrayWrapper
     */
    public function setData($name, $value)
    {
        $this->_array[$name] = $value;
        return $this;
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_array[$name]);
    }

    /**
     * Unset magix
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->_array[$name]);
    }

    /**
     * Check whether all elements exist in wrapped array
     *
     * @param array $elements
     * @return bool
     */
    public function allElementsExist($elements = [])
    {
        if (!is_array($elements)) {
            $elements = func_get_args();
        }
        if (empty($elements)) {
            return false;
        }
        $result = true;
        foreach ($elements as $index) {
            $result &= isset($this->_array[$index]);
        }
        return $result;
    }
}
