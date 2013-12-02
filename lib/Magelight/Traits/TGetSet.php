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

namespace Magelight\Traits;

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
     * @return TGetSet
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
