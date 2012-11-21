<?php

namespace Magelight\Webform\Models\Validation\Rules;

class PregMatch extends AbstractRule
{
    protected $_error = 'Field %1$s must match regexp "%2$s"';

    public function check($value, $args)
    {
        if (isset($args[0])) {
            return preg_match($args[0], $value) > 0;
        }
        return false;
    }
}