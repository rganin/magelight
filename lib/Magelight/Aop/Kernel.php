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

namespace Magelight\Aop;

/**
 * Class Kernel
 * @package Magelight\Aop
 *
 * @method static \Magelight\Aop\Kernel getInstance()
 */
class Kernel
{
    use \Magelight\Traits\TForgery;

    protected $_aspects = [];

    public function registerAspect($pattern, $advice)
    {
        $pattern = $this->_disruptPattern($pattern);
        $this->_aspects[$pattern['class']][$pattern['call']] = $advice;
    }

    protected function _getClassStack($className)
    {
        $stack = [$className];
        while ($parent = get_parent_class($className)) {
            $stack[] = $parent;
        }
        return $stack;
    }

    public function processClass($className)
    {
        foreach ($this->_getClassStack($className) as $class) {
            foreach ($this->_aspects as $classPattern => $aspect) {

            }
        }
    }

    /**
     * Get pattern disrupted into array of params
     *
     * @param string $pattern
     * @return mixed
     * @throws \Exception
     */
    public function _disruptPattern($pattern)
    {
        if (!preg_match_all(
            '/(?P<class>[a-zA-Z0-9\_\*]+)((?P<static>\:\:)|(?P<normal>\-\>))'
                . '(?P<call>[a-zA-Z0-9\_\*]+)(?P<is_method>[\(\)]*)/',
            $pattern, $matches
        )) {
            throw new \Exception('Incorrect pattern: ' . $pattern);
        }
        foreach ($matches as $key => $value) {
            if (!is_string($key)) {
                unset($matches[$key]);
            } else {
                $matches[$key] = $value[0];
            }
        }
        $matches['is_method'] = !empty($matches['is_method']);
        $matches['static'] = !empty($matches['static']);
        $matches['normal'] = !empty($matches['normal']);
        return $matches;
    }
}
