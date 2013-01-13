<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 06.01.13
 * Time: 16:04
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class Time24 extends AbstractRule
{
    protected $_error = 'Field %s must be a valid 24h formatted time';

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'time24';

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
        return strtotime($value) !== false;
    }
}