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

namespace Magelight\Aop\Entity;

/**
 * Class Decorator
 * @package Magelight\Aop\Entity
 */
class Decorator
{
    /**
     * AOP decorator suffix
     */
    const DECORATOR_SUFFIX = '_AOP_DECORATOR';

    /**
     * @var string
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_parent;

    /**
     * @var string
     */
    protected $_namespace;

    /**
     * @var \ReflectionMethod[]
     */
    protected $_methods = [];

    /**
     * Class template
     *
     * @var string
     */
    protected $_templateClass = '<?php

namespace <namespace>;

class <class> extends <parent>
{
    <methods>
}';

    /**
     * Method template
     *
     * @var string
     */
    protected $_templateMethod = 'public function <method>(<signature>)
    {
        $reflectionMethod = new ReflectionMethod(get_parent_class($this), "foo");
        return \Magelight\Aop\Kernel::getInstance()->assumeAdvices($reflectionMethod, $this, func_get_args());
    }';

    /**
     * Constructor
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $class = new \ReflectionClass($className);
        $this->_parent = $className;
        $this->_class = $className . self::DECORATOR_SUFFIX;
        $this->_namespace = $class->getNamespaceName();
        $this->_methods = $class->getMethods();
    }

    /**
     * Assemble class from template
     *
     * @return string|mixed
     */
    public function assemble()
    {
        $methods = [];

        foreach ($this->_methods as $methodReflection) {
            $method = [
                '<name>'      => $methodReflection->getName(),
                '<signature>' => $this->getMethodSignature($methodReflection)
            ];
            $methods[] = str_replace(array_keys($method), array_values($method), $this->_templateMethod);
        }

        $class = [
            '<namespace>' => $this->_namespace,
            '<class>'     => $this->_class,
            '<methods>'   => implode(PHP_EOL . '  ', $methods),
            '<parent>'    => $this->_parent
        ];

        return str_replace(array_keys($class), array_values($class), $this->_templateClass);
    }

    /**
     * Get method signature from reflection
     *
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getMethodSignature(\ReflectionMethod $method)
    {
        $signature = '';
        $i = 0;
        foreach ($method->getParameters() as $paramReflection) {
            $param = '$';
            $param.= $paramReflection->getName();
            if ($default = $paramReflection->getDefaultValue()) {
                $param .= '=' . $default;
            }
            $signature .= ($i > 0 ? ',' : '') . $param;
            $i++;
        }
        return $signature;
    }
}
