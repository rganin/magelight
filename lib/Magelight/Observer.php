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

namespace Magelight;

/**
 * Observer (application event handler) class
 *
 * @method static $this forge($arguments = [])
 */
abstract class Observer
{
    /**
     * Unsing forgery
     */
    use Traits\TForgery;

    /**
     * Observer arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Forgery constructor
     *
     * @param array $arguments
     */
    public function __forge($arguments = [])
    {
        $this->arguments = $arguments;
    }

    /**
     * Execute observer
     *
     * @return Observer
     */
    abstract public function execute();

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : null;
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->arguments[$name]);
    }

    /**
     * Unset magic
     *
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->arguments[$name]);
    }
}
