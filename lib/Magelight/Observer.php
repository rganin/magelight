<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 12.12.12
 * Time: 17:51
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

/**
 * @method static \Magelight\Observer forge($arguments = [])
 */
abstract class Observer
{
    use Forgery;

    /**
     * Observer arguments
     *
     * @var array
     */
    protected $_arguments = [];

    /**
     * Forgery constructor
     *
     * @param array $arguments
     */
    public function __forge($arguments = [])
    {
        $this->_arguments = $arguments;
    }

    /**
     * Execute observer
     *
     * @return Observer
     */
    public function execute()
    {
        return $this;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_arguments[$name] = $value;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->_arguments[$name]) ? $this->_arguments[$name] : null;
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_arguments[$name]);
    }
}
