<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 21.04.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
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