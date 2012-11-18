<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 14:37
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\File forge()
 */
class File extends Input
{
    public function __forge()
    {
        $this->setType('file');
    }
}