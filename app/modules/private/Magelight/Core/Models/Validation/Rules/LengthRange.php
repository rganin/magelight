<?php

namespace Magelight\Core\Models\Validation\Rules;

class LengthRange extends AbstractRule
{

    protected $_error = 'Field %1$s must contain from %2$d to %3$d symbols';

    public function check($value, $args)
    {
        if (isset($args[0], $args[1])) {
            return (mb_strlen($value) >= $args[0]) && (mb_strlen($value) <= $args[1]);
        }
        return false;
    }
}