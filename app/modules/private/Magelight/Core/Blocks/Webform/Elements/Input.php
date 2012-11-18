<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 16.11.12
 * Time: 23:17
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Input forge()
 */
class Input extends \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field
{
    protected $_tag = 'input';

    protected $_empty = true;

    public function __forge()
    {
        $this->setType('text');
    }
    /**
     * @param $type
     * @return Input
     */
    public function setType($type)
    {
        return $this->setAttribute('type', $type);
    }

    public function setDisabled()
    {
        return $this->setAttribute('disabled', 'disabled');
    }
}