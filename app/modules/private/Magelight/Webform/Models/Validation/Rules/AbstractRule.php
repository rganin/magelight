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
 * @method static \Magelight\Webform\Models\Validation\Rules\AbstractRule
 *         forge(\Magelight\Webform\Models\Validation\Checker $checker)
 *
 */
abstract class AbstractRule
{
    use \Magelight\Forgery;

    /**
     * Translation context
     */
    const TRANSLATE_CONTEXT = 'validation';

    /**
     * Validation error pattern
     *
     * @var string
     */
    protected $_error = 'Common validation error';

    /**
     * Rule arguments
     *
     * @var array
     */
    protected $_arguments = [];

    /**
     * Field title
     *
     * @var string
     */
    protected $_fieldTitle = null;

    /**
     * Checker backreference for chaining
     *
     * @var \Magelight\Webform\Models\Validation\Checker
     */
    protected $_checker = null;

    /**
     * Forgery constructor
     *
     * @param \Magelight\Webform\Models\Validation\Checker $checker
     */
    public function __forge(\Magelight\Webform\Models\Validation\Checker $checker)
    {
        $this->_checker = $checker;
    }

    /**
     * Get checker backreference
     *
     * @return \Magelight\Webform\Models\Validation\Checker
     */
    public function checker()
    {
        return $this->_checker;
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
        $this->_fieldTitle = $fieldName;
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
        $this->_arguments = $arguments;
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
        $this->_error = $errorText;
        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        $args = $this->_arguments;
        array_unshift($args, $this->_fieldTitle);
        return @\Magelight::__($this->_error, 1, self::TRANSLATE_CONTEXT, $args);
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
}
