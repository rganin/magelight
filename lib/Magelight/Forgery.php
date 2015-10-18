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
 * Class Forgery
 * @package Magelight
 */
class Forgery
{
    /**
     * Classes overrides
     *
     * @var array
     */
    protected $classPreferences = [];

    /**
     * Interfaces for classes overrides
     *
     * @var array
     */
    protected $classOverridesInterfaces = [];

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
        $this->classPreferences[$sourceClassName] = $preferenceClassName;
    }

    /**
     * Get class name according to runtime overrides
     *
     * @param string $className
     * @return mixed
     */
    final public function getClassName($className)
    {
        while (!empty($this->classPreferences[$className])) {
            $className = $this->classPreferences[$className];
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
        if (!isset($this->classOverridesInterfaces[$className])) {
            $this->classOverridesInterfaces[$className] = [];
        }
        $this->classOverridesInterfaces[$className][] = $interfaceName;
    }

    /**
     * Add class interface check for overriden class
     *
     * @param string $className
     * @return array
     */
    final public function getClassInterfaces($className)
    {
        return !empty($this->classOverridesInterfaces[$className])
        && is_array($this->classOverridesInterfaces[$className])
            ? $this->classOverridesInterfaces[$className]
            : [];
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
