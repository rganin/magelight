<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 13.01.13
 * Time: 23:44
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Blocks\Elements;

class InputHidden extends Input
{
    public function __forge()
    {
        parent::__forge();
        $this->setType('hidden');
    }
}
