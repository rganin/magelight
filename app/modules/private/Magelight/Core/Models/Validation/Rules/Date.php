<?php

namespace Magelight\Core\Models\Validation\Rules;

class DateAndTime extends AbstractRule
{

    protected $_error = 'Field %1$s must be a valid date';

    public function check($value, $args)
    {
        return strtotime($value) !== false;
    }
}