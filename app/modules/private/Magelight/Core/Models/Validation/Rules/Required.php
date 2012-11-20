<?php

namespace Magelight\Core\Models\Validation\Rules;

class Required extends AbstractRule{

    protected $_error = 'Field %1$s is required';

    public function check($value, $args)
    {
        return !empty($value) && ($value !== 0) && $value !== '' && !is_null($value) && $value !=='0';
    }
}