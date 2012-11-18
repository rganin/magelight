<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 2:49
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Textarea forge()
 */
class Textarea extends \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field
{
    protected $_tag = 'textarea';

    public function setValue($value)
    {
        return $this->setContent($value);
    }
}