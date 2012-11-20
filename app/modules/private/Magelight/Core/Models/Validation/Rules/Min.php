<?php

namespace Magelight\Core\Models\Validation\Rules;

class Min extends AbstractRule
{

    protected $_error = 'Field %1$s shouldn`t be less than %2$d';

    public function check($value, $args)
    {
        if (isset($args[0])) {
            return $value >= $args[0];
        }
        return false;
    }
}
