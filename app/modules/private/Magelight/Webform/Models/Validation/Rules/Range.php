<?php

namespace Magelight\Webform\Models\Validation\Rules;

class Range extends AbstractRule
{
    protected $_error = 'Field %1$s must be between %2$d and %3$d';

    public function check($value, $args)
    {
        if (isset($args[0], $args[1])) {
            return ($value >= $args[0]) && ($value <= $args[1]);
        }
        return false;
    }
}
