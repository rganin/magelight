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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Models\Validation\Rules;

/**
 * @method static \Magelight\Webform\Models\Validation\Rules\AbstractRule
 *         forge(\Magelight\Webform\Models\Validation\Checker $checker)
 *
 */
abstract class AbstractRule
{
    use \Magelight\Traits\TForgery;

    /**
     * Translation context
     */
    const TRANSLATE_CONTEXT = 'validation';

    /**
     * Custom error string
     *
     * @var string
     */
    protected $error;

    /**
     * Rule arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Field title
     *
     * @var string
     */
    protected $fieldTitle = null;

    /**
     * Checker backreference for chaining
     *
     * @var \Magelight\Webform\Models\Validation\Checker
     */
    protected $checker = null;

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $frontValidatorRule = '';

    /**
     * Forgery constructor
     *
     * @param \Magelight\Webform\Models\Validation\Checker $checker
     */
    public function __forge(\Magelight\Webform\Models\Validation\Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Get checker backreference
     *
     * @return \Magelight\Webform\Models\Validation\Checker
     */
    public function checker()
    {
        return $this->checker;
    }

    /**
     * Get checker backreference (for chaining rules).
     * Alias for ::checker() method
     *
     * @return \Magelight\Webform\Models\Validation\Checker
     */
    public function chainRule()
    {
        return $this->checker();
    }

    /**
     * Set field title
     *
     * @param string $fieldName
     *
     * @return AbstractRule
     */
    public function setFieldTitle($fieldName)
    {
        $this->fieldTitle = $fieldName;
        return $this;
    }

    /**
     * Set rule arguments for checking
     *
     * @param array $arguments
     * @return AbstractRule
     */
    public function setArguments($arguments = [])
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Set custom error text pattern
     *
     * @param string $errorText
     * @return AbstractRule
     */
    public function setCustomError($errorText)
    {
        $this->error = $errorText;
        return $this;
    }

    /**
     * Get error arguments
     *
     * @return array
     */
    protected function getErrorArguments()
    {
        $args = $this->arguments;
        array_unshift($args, $this->fieldTitle);
        return $args;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        if (!empty($this->error)) {
            return __($this->error, $this->getErrorArguments());
        }
        return __('Common validation error');
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
    abstract public function check($value);

    /**
     * Get params array or raw param for front validaition in JQuery Validator
     *
     * @return mixed|array|bool|int
     */
    public function getFrontValidationParams()
    {
        return true;
    }

    /**
     * Get front validation rule name
     *
     * @return string
     */
    public function getFrontValidationRuleName()
    {
        return $this->frontValidatorRule;
    }
}
