<?php

namespace Magelight;

class Forgery
{
    /**
     * Classes overrides
     *
     * @var array
     */
    protected $_classOverrides = [];

    /**
     * Interfaces for classes overrides
     *
     * @var array
     */
    protected $_classOverridesInterfaces = [];

    /**
     * Constructor
     */
    protected function __construct()
    {

    }

    /**
     * Get instance
     *
     * @return $this
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Add class to override
     *
     * @param string $sourceClassName
     * @param string $replacementClassName
     */
    final public function addClassOverride($sourceClassName, $replacementClassName)
    {
        $this->_classOverrides[$sourceClassName] = $replacementClassName;
    }

    /**
     * Get class name according to runtime overrides
     *
     * @param string $className
     * @return mixed
     */
    final public function getClassName($className)
    {
        while (!empty($this->_classOverrides[$className])) {
            $className = $this->_classOverrides[$className];
        }
        return $className;
    }

    /**
     * Add interface check to overriden class
     *
     * @param string $className
     * @param string $interfaceName
     */
    final public function addClassOverrideInterface($className, $interfaceName)
    {
        if (!isset($this->_classOverridesInterfaces[$className])) {
            $this->_classOverridesInterfaces[$className] = [];
        }
        $this->_classOverridesInterfaces[$className][] = $interfaceName;
    }

    /**
     * Add class interface check for overriden class
     *
     * @param string $className
     * @return array
     */
    final public function getClassInterfaces($className)
    {
        return !empty($this->_classOverridesInterfaces[$className])
        && is_array($this->_classOverridesInterfaces[$className])
            ? $this->_classOverridesInterfaces[$className]
            : [];
    }

    /**
     * Check class interfaces (checks that class is derived from one of overriden ones)
     *
     * @param string $className
     * @return bool
     */
    final protected function _checkInterfaces($className)
    {
        $requiredInterfaces = $this->getClassInterfaces($className);
        $implementedInterfaces = class_implements($className, true);
        foreach ($requiredInterfaces as $interface) {
            if (!isset($implementedInterfaces[$interface])) {
                return false;
            }
        }
        return true;
    }
}