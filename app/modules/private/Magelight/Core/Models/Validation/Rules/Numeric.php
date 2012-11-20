<?php

namespace Magelight\Core\Models\Validation\Rules;

class Numeric extends AbstractRule
{
    protected $_error = 'Field %1$s must numeric';

    public function check($value, $args)
    {
        return is_numeric($value);
    }
}
