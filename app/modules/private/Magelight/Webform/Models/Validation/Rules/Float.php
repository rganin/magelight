<?php

namespace Magelight\Webform\Models\Validation\Rules;

class Float extends AbstractRule{

    protected $_error = 'Field %1$s must a float value';

    public function check($value, $args)
    {
        return is_numeric($value) || is_float($value);
    }
}