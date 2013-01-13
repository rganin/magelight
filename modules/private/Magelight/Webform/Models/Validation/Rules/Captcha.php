<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 23.12.12
 * Time: 1:51
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class Captcha extends AbstractRule
{
    /**
     * Validation error pattern
     *
     * @var string
     */
    protected $_error = 'Please enter a valid protection code';

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'captcha';

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
        return \Magelight\Webform\Models\Captcha\Captcha::forge()->check($value);
    }
}