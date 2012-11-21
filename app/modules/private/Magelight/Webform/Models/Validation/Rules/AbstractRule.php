<?php

namespace Magelight\Webform\Models\Validation\Rules;

/**
 * Abstact rule class
 *
 * @author iddqd
 *
 */
abstract class AbstractRule implements \Magelight\Webform\Models\Validation\Rules\RuleInterface
{
    use \Magelight\Forgery;

    protected $_error = 'Common validation error';
    /**
     * Check field validity
     *
     * @param mixed $value
     * @param array $arguments
     * @return boolean
     */
    abstract public function check($value, $arguments);

    /**
     * Get default validation error
     *
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

}