<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 13:38
 * To change this template use File | Settings | File Templates.
 */
namespace Magelight\Core\Blocks\Webform\Elements;
/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Radio forge()
 */
class Radio extends Input
{
    public function __forge()
    {
        $this->setAttribute('type', 'radio');
        $this->setValue('true');
    }

    public function setChecked()
    {
        return $this->setAttribute('checked', 'checked');
    }
}