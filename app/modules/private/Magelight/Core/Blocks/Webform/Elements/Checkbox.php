<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 11:27
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Checkbox forge()
 */
class Checkbox extends Input
{
    public function __forge()
    {
        $this->setAttribute('type', 'checkbox');
        $this->setValue('true');
    }

    public function setChecked()
    {
        return $this->setAttribute('checked', 'checked');
    }
}