<?php

namespace Magelight\Webform\Models\Validation\Rules;

class MinLength extends AbstractRule
{

    protected $_error = 'Field %1$s shouldn`t be shorter than %2$d';

    public function check($value, $args)
    {
        if (isset($args[0])) {
        	
            return mb_strlen($value) >= $args[0];
        }
        return false;
    }
}
