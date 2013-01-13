<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 13.01.13
 * Time: 19:28
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class EqualsToField extends AbstractRule
{
    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'equalTo';

    /**
     * Error string
     *
     * @var string
     */
    protected $_error = 'Field %s must be equal to field %3$s';

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
        return $this->checker()->getValidator()->getFieldValue($value)
            == $this->checker()->getValidator()->getFieldValue($this->_arguments[0]);
    }

    public function getFrontValidationParams()
    {
        return $this->_arguments[0];
    }
}