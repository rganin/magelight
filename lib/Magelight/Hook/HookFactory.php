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

namespace Magelight\Hook;

use Magelight\Config;

/**
 * Class HookFactory
 * @package Magelight\Hook
 */
class HookFactory
{
    /**
     * @var array
     * [
     *      'Subject\Class' => [
     *          'subjectMethod' => [
     *              'before' => [['class' => 'Hook\Class1', 'method' => 'hookMethod'], ['class => 'hook\class2', 'method' => 'method2']],
     *              'after' => [['class' => 'Hook\Class3', 'method' => 'afterHookedMethod']],
     *          ],
     *      ],
     *      ....
     * ]
     */
    protected $classHooks = [];

    /**
     * Array of classes and their parents and interfaces
     *
     * [
     *      'class1' => ['parent', 'parent2', 'interface']
     * ]
     *
     * @var array
     */
    protected $classParents = [];

    /**
     * Get instance of factory
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
     * HookFactory constructor.
     */
    protected function __construct()
    {}

    /**
     * Load hooks configuration
     */
    public function loadHooksConfig()
    {
        $config = Config::getInstance()->getConfig('global/hooks');
        foreach ($config->hook as $hook) {
            list($subjectClass, $subjectMethod) = explode('::', (string)$hook->subject);
            if (!empty($hook->before)) {
                list ($hookClass, $hookMethod) = explode('::', (string)$hook->before);
                $this->classHooks[$subjectClass][$subjectMethod]['before'][(string)$hook->before] = [
                    'class' => $hookClass,
                    'method' => $hookMethod
                ];
            }
            if (!empty($hook->after)) {
                list ($hookClass, $hookMethod) = explode('::', (string)$hook->after);
                $this->classHooks[$subjectClass][$subjectMethod]['after'][(string)$hook->after] = [
                    'class' => $hookClass,
                    'method' => $hookMethod
                ];
            }
            if (!empty($hook->mute)) {
                unset($this->classHooks[$subjectClass][$subjectMethod]['before'][(string)$hook->mute]);
                unset($this->classHooks[$subjectClass][$subjectMethod]['after'][(string)$hook->mute]);
            }
        }
        $this->classHooks;
    }

    /**
     * Check that class has hooks
     *
     * @param string $className
     * @return bool
     */
    public function hasHooks($className) {
        if (isset($this->classHooks[$className])) {
            return true;
        }
        foreach (array_keys($this->classHooks) as $hookedClassName) {
            if (isset($this->getClassParentsAndInterfaces($className)[$hookedClassName])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get class parents and interfaces
     *
     * @param string $className
     * @return array
     */
    protected function getClassParentsAndInterfaces($className)
    {
        if (isset($this->classParents[$className])) {
            return $this->classParents[$className];
        }
        $parents = class_parents($className, false);
        $this->classParents[$className] = array_merge(class_implements($className, false), $parents);
        return $this->classParents[$className];
    }

    /**
     * Forge hooked class
     *
     * @param string $className
     * @return object
     */
    public function forgeHookedClass($className)
    {
        $methods = isset($this->classHooks[$className]) ? $this->classHooks[$className] : [];

        foreach ($this->getClassParentsAndInterfaces($className) as $parent) {
            if (isset($this->classHooks[$parent])) {
                $methods = array_merge_recursive($this->classHooks[$parent], $methods);
            }
        }
        $classReflection = new \ReflectionClass($className);
        $code = "return new class extends $className {" . PHP_EOL;

        foreach ($methods as $methodName => $methodHooks) {
            $methodReflection = $classReflection->getMethod($methodName);
            $methodParams = $methodReflection->getParameters();
            $paramsArray = [];
            // prepare method signature
            foreach ($methodParams as $param) {
                $class = (string)$param->getType();
                $paramString = (!empty($class) ? $class . ' ' : '');
                $name = $param->getName();
                $paramString .= "\$$name";
                try {
                    $optionalValue = $param->getDefaultValue();
                    $paramString .= ' = ' . var_export($optionalValue, true);
                } catch (\ReflectionException $e) {
                }
                $paramsArray [] = $paramString;
            }
            $methodParams = implode(',', $paramsArray);
            $code .= "public function $methodName({$methodParams}) {" . PHP_EOL;
            $code .= "\$args = \$initialArgs = func_get_args();" . PHP_EOL;
            $code .= "\$emptyArgs = empty(\$args);";
            if (!empty($methodHooks['before'])) {
                foreach ($methodHooks['before'] as $beforeMethodHook) {
                    $code .= "\$args = call_user_func_array([{$beforeMethodHook['class']}::getInstance(\$this), "
                        . "'{$beforeMethodHook['method']}'], \$args);" . PHP_EOL;
                    $code .= "if (!\$emptyArgs && (\$args === null || !is_array(\$args))) {" . PHP_EOL;
                    $code .= "throw new \\Magelight\\Exception("
                        . "'Hook {$beforeMethodHook['class']}::{$beforeMethodHook['method']} must return array "
                        . " of arguments');" . PHP_EOL;
                    $code .= '}' . PHP_EOL;
                }
            }
            $code .= "\$result = call_user_func_array([\$this, 'parent::$methodName'], \$args);" . PHP_EOL;
            if (!empty($methodHooks['after'])) {
                foreach ($methodHooks['after'] as $afterMethodHook) {
                    $code .= "\$result = call_user_func_array([{$afterMethodHook['class']}::getInstance(\$this), "
                        . "'{$afterMethodHook['method']}'], [\$result]);" . PHP_EOL;
                }
            }
            $code .= "return \$result;" . PHP_EOL;
            $code .= '}' . PHP_EOL;
        }
        $code .= '};' . PHP_EOL;
        return eval($code);
    }
}
