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
 * @method static \Magelight\Webform\Models\Validation\Rules\DateTimeRange
 *         forge(\Magelight\Webform\Models\Validation\Checker $checker)
 *
 */
class DateRange extends AbstractRule
{
    /**
     * Validation error pattern
     *
     * @var string
     */
    protected $_error = 'Field %s must be a valid date between %s and %s';

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'daterange';

    /**
     * Set arguments for checking
     *
     * @param array $arguments
     *
     * @return AbstractRule
     * @throws \Magelight\Exception
     */
    public function setArguments($arguments = [])
    {
        $dateRule = DateTime::forge();
        if (!isset($arguments[0]) || !isset($arguments[1])) {
            throw new \Magelight\Exception('Arguments for DatRange rule must be passed.');
        }
        if (!$dateRule->check($arguments[0]) || !$dateRule->check($arguments[1])) {
            throw new \Magelight\Exception('Arguments for DatRange rule must be valid date strings.');
        }
        return parent::setArguments($arguments);
    }

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
        $dateRule = new DateTime();
        $dateRule->setArguments([$this->_arguments[0]]);
        if (!$dateRule->check($value)) {
            return false;
        } else {
            $ret = true;
            $value = strtotime($value);
            return ($value >= strtotime($this->_arguments[0])) && ($value <= strtotime($this->_arguments[1]));
        }
    }

    /**
     * Get params array or raw param for front validaition in JQuery Validator
     *
     * @return mixed|array|bool|int
     */
    public function getFrontValidationParams()
    {
        return $this->_arguments;
    }
}
