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

namespace Magelight;

/**
 * Class ArrayWrapper
 * @package Magelight
 *
 * @method static $this forge($array, $defaultReturnValue = null)
 */
class ArrayWrapper
{
    use Traits\TForgery;

    /**
     * Default return value for getters
     *
     * @var mixed|null
     */
    protected $defaultReturnValue = null;

    /**
     * Array to be wrapped
     *
     * @var array
     */
    protected $array = [];

    /**
     * Forgery constructor
     *
     * @param array $array
     * @param null|mixed $defaultReturnValue
     */
    public function __forge($array, $defaultReturnValue = null)
    {
        $this->array = $array;
        $this->defaultReturnValue = $defaultReturnValue;
    }

    /**
     * Magic getter
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->array[$name]) ? $this->array[$name] : $this->defaultReturnValue;
    }

    /**
     * Magic setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->array[$name] = $value;
    }

    /**
     * Get array data
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getData($name, $default = null)
    {
        return isset($this->array[$name]) ? $this->array[$name] : $default;
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
        $this->array[$name] = $value;
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
        return isset($this->array[$name]);
    }

    /**
     * Unset magix
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->array[$name]);
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
            $result &= isset($this->array[$index]);
        }
        return (bool)$result;
    }
}
