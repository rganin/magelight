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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Models\Validation\Rules;

/**
 * @method static \Magelight\Webform\Models\Validation\Rules\MinLength
 *         forge(\Magelight\Webform\Models\Validation\Checker $checker)
 *
 */
class MinLength extends AbstractRule
{
    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'minlength';

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
        return (strlen(trim($value)) >= $this->_arguments[0]);
    }

    /**
     * Get params array or raw param for front validaition in JQuery Validator
     *
     * @return mixed|array|bool|int
     */
    public function getFrontValidationParams()
    {
        return $this->_arguments[0];
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        if (!empty($this->_error)) {
            return $this->_error;
        }
        return __('Field %s must contain at least %s characters', $this->getErrorArguments());
    }
}
