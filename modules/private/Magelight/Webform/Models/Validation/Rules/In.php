<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 13.01.13
 * Time: 22:53
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class In extends AbstractRule
{

    protected $_error = 'Field %s must have a valid value';

    /**
     * Check value with rule
     * Returns:
     *    - true if rule passed.
     *    - false if value doesn`t match the rule.
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value)
    {
        return is_array($this->_arguments[0]) ? in_array($value, $this->_arguments[0]) : false;
    }
}