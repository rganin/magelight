<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 17.11.12
 * Time: 16:03
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Hint forge()
 */
class Hint extends Abstraction\Element
{
    protected $_tag = 'span';

    public function __forge()
    {
        $this->setClass('help-inline');
    }
}