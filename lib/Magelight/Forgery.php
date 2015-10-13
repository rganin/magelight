<?php

namespace Magelight;

class Forgery
{
    /**
     * Classes overrides
     *
     * @var array
     */
    protected $_classPreferences = [];

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
     * @param string $preferenceClassName
     */
    final public function setPreference($sourceClassName, $preferenceClassName)
    {
        $this->_classPreferences[$sourceClassName] = $preferenceClassName;
    }

    /**
     * Get class name according to runtime overrides
     *
     * @param string $className
     * @return mixed
     */
    final public function getClassName($className)
    {
        while (!empty($this->_classPreferences[$className])) {
            $className = $this->_classPreferences[$className];
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

    /**
     * Load classes preferences from array
     *
     * @param array $preferenceList
     * @return $this
     */
    final public function loadPreferences(array $preferenceList)
    {
        if (!empty($preferenceList)) {
            foreach ($preferenceList as $preference) {
                if (!empty($preference->old) && !empty($preference->new)) {
                    $this->setPreference(
                        trim($preference->old, " \\/ "),
                        trim($preference->new, " \\/ ")
                    );
                    if (!empty($preference->interface)) {
                        foreach ($preference->interface as $interface) {
                            $this->addClassOverrideInterface(
                                (string)$preference->new, trim($interface, " \\/ ")
                            );
                        }
                    }
                }
            }
        }
        return $this;
    }
}