<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 16.11.12
 * Time: 23:41
 * To change this template use File | Settings | File Templates.
 */
namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Label forge()
 */
class Label extends Abstraction\Element
{
    protected $_tag = 'label';

    public function __forge()
    {
        $this->addClass('control-label');
    }

    /**
     * @param $forId
     * @return Label
     */
    public function setFor($forId)
    {
        return $this->setAttribute('for', $forId);
    }

}