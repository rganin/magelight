<?php


namespace Magelight\Core\Models\Validation\Rules;

class Max extends AbstractRule
{
    protected $_error = 'Field %1$s must be less than %2$d';

    public function check($value, $args)
    {
        if (isset($args[0])) {
            return $value <= $args[0];
        }
        return false;
    }
}
