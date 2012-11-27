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

namespace Magelight\Webform\Models\Validation;

/**
 * @method static \Magelight\Webform\Models\Validation\Checker forge($fieldName, $fieldAlias = null) - forgery
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule max($max) - maximum value rule
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule required() - Field is required
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule dateTime() - Field must be a date time string
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule dateTimeRange($minDate, $maxDate)
 * - Field is in range between two dates
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule email() - Field must be a valid email
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule float() - Field must be a float value
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule maxLength($max) - Field has max length
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule min($min) - Field must be not less than
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule minLength($min) - Field must be longer than
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule numeric() - Field must be numeric
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule pregMatch() - Field must match regex
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule range($min, $max)
 * - Field must be between two values
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule rangeLength($min, $max)
 * - Field must contain from $min to $max chars
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule urlHttp() - Field must be a valid http or https URL
 */
class Checker
{
    use \Magelight\Forgery;

    /**
     * Field name
     *
     * @var string
     */
    protected $_fieldName = null;

    /**
     * Field alias
     *
     * @var string
     */
    protected $_fieldAlias = null;

    /**
     * Form highlight id
     *
     * @var string
     */
    protected $_highlightId = null;

    /**
     * Rules to check with
     *
     * @var array
     */
    protected $_rules = [];

    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * Break on first error flag
     *
     * @var bool
     */
    protected $_breakOnFirst = false;

    /**
     * Forgery constructor
     *
     * @param $fieldName
     * @param string $fieldAlias
     */
    public function __forge($fieldName, $fieldAlias = null)
    {
        $this->_fieldName = $fieldName;
        $this->setFieldAlias($fieldAlias);
    }

    /**
     * Set Field alias name
     *
     * @param string $fieldAlias
     * @return \Magelight\Webform\Models\Validation\Checker
     */
    public function setFieldAlias($fieldAlias = null)
    {
        if (empty($fieldAlias)) {
            $this->_fieldAlias = $this->_fieldName;
        } else {
            $this->_fieldAlias = $fieldAlias;
        }
        return $this;
    }

    /**
     * Set highlight ID
     *
     * @param string $fieldId
     * @return Checker
     */
    public function setHighlightId($fieldId)
    {
        $this->_highlightId = $fieldId;
        return $this;
    }

    /**
     * Add rule to checker
     *
     * @param Rules\AbstractRule $rule
     * @return Checker
     */
    public function addRule(Rules\AbstractRule $rule)
    {
        $this->_rules[get_class($rule)] = $rule;
        return $this;
    }

    /**
     * Call magix
     *
     * @param $name
     * @param $arguments
     * @return Rules\AbstractRule
     * @throws \Magelight\Exception
     */
    public function __call($name, $arguments)
    {
        $className = __NAMESPACE__ . '\\Rules\\' . ucfirst($name);
        $rule = call_user_func_array([$className, 'forge'], [$this]);

        if (!$rule instanceof Rules\AbstractRule) {
            throw new \Magelight\Exception(
                "Trying to add unknown rule '$className' in "
                . __CLASS__
                . " for field {$this->_fieldName} ({$this->_fieldAlias})."
            );
        }
        /* @var $rule Rules\AbstractRule */
        $this->addRule($rule->setArguments($arguments)->setFieldTitle($this->_fieldAlias));
        return $rule;
    }

    /**
     * Check value for validity with set of rules
     *
     * @param string $value
     * @return bool
     */
    public function check($value)
    {
        $result = true;
        foreach ($this->_rules as $rule) {
            /* @var $rule Rules\AbstractRule */
            if (!$rule->check($value)) {
                $this->_errors[get_class($rule)] = Error::forge($rule->getError(), $this->_highlightId);
                if ($this->_breakOnFirst) {
                    return false;
                }
                $result = false;
            }
        }
        return (bool) $result;
    }

    /**
     * Set break on first error flag
     *
     * @param bool $flag
     * @return Checker
     */
    public function breakOnFirst($flag = true)
    {
        $this->_breakOnFirst = $flag;
        return $this;
    }

    /**
     * Get checking errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
