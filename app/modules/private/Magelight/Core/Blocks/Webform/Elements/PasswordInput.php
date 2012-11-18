<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 12:21
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\PasswordInput forge()
 */
class PasswordInput extends Input
{
    public function __forge()
    {
        $this->setAttribute('type', 'password');
    }
}