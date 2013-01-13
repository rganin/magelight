<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 22.12.12
 * Time: 23:37
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

/**
 * Getter & setter trait
 */
trait TGetSet
{
    /**
     * GetSet Target - an array or object to store values
     *
     * @var array
     */
    protected $___getSetTarget = [];

    /**
     * Default return value for getter
     *
     * @var mixed
     */
    protected $___default = null;

    /**
     * Set target for getters and setters
     *
     * @param array|Object $target
     * @return GetSet
     */
    protected function setGetSetTarget(&$target)
    {
        $this->___getSetTarget = &$target;
        return $this;
    }

    /**
     * Get value magic
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return is_object($this->___getSetTarget) ?
            (isset($this->___getSetTarget->$name) ? $this->___getSetTarget->$name : $this->___default) :
            (isset($this->___getSetTarget[$name]) ? $this->___getSetTarget[$name] : $this->___default);
    }

    /**
     * Set value magic
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (is_object($this->___getSetTarget)) {
            $this->___getSetTarget->$name = $value;
        } else {
            $this->___getSetTarget[$name] = $value;
        }
    }

    /**
     * Isset value magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (is_object($this->___getSetTarget)) {
            return isset($this->___getSetTarget);
        }
        return isset($this->___getSetTarget[$name]);
    }

    /**
     * Unset value magic
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if (is_object($this->___getSetTarget)) {
            unset($this->___getSetTarget);
        }
        unset($this->___getSetTarget[$name]);
    }
}
