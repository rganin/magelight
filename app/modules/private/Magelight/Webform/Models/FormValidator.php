<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 17:54
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models;

class FormValidator extends Validator
{
    public function resultJson($prettyPrint = false)
    {
        return json_encode($this->result(), $prettyPrint ? JSON_PRETTY_PRINT : 0);
    }
}