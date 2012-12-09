<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:34
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class Equals extends AbstractRule
{
    /**
     * Validation error pattern
     *
     * @var string
     */
    protected $_error = 'Field %s must be equal to %s';

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
        return $value === $this->_arguments[0];
    }
}