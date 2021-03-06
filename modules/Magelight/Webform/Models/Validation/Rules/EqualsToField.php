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

namespace Magelight\Webform\Models\Validation\Rules;

/**
 * Class EqualsToField
 *
 * @package Magelight\Webform\Models\Validation\Rules
 */
class EqualsToField extends AbstractRule
{
    /**
     * @var string
     */
    protected $error = 'Field %s must be equal to field %3$s';

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $frontValidatorRule = 'equalTo';

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
            == $this->checker()->getValidator()->getFieldValue($this->arguments[0]);
    }

    /**
     * Get frontend validator validation params
     *
     * @return array|bool|int|mixed
     */
    public function getFrontValidationParams()
    {
        return '#' . $this->checker()->getValidator()->getForm()->getFieldIdByName($this->arguments[0]);
    }
}
