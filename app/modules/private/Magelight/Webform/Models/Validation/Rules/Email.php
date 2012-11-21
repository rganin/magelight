<?php

namespace Magelight\Webform\Models\Validation\Rules;

class Email extends AbstractRule
{
    protected $_error = 'Field %1$s must a valid e-mail address';

    public function check($value, $args)
    {
        $regex = isset($args[0]) ?
            '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' :
            '/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
        return preg_match($regex, trim($value)) > 0;
    }
}