<?php

namespace Magelight\Webform\Models\Validation\Rules;

class MaxLength extends AbstractRule
{

    protected $_error = 'Field %1$s shouldn`t be longer than %2$d characters';

    public function check($value, $args)
    {
        if (isset($args[0])) {
            return mb_strlen($value) <= $args[0];
        }
        return false;
    }
}
