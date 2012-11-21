<?php

namespace Magelight\Webform\Models\Validation\Rules;

class Url extends AbstractRule
{
    protected $_error = 'Field %1$s must be a valid URL link';

    public function check($value, $args)
    {
        $regex = "/^(http(s?):\\/\\/|ftp:\\/\\/{1})((\w+\.)+)\w{2,}(\/?)$/i";
        return preg_match($regex, trim($value)) > 0;
    }
}
