<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 17.11.12
 * Time: 21:53
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Button forge()
 * @method \Magelight\Core\Blocks\Webform\Elements\Button addClass($content)
 */
class Button extends Abstraction\Element
{
    protected $_tag = 'button';

    public function __forge()
    {
        $this->addClass('btn');
    }

    /**
     * @param $type
     * @return Button
     */
    public function setType($type)
    {
        return $this->setAttribute('type', $type);
    }

    /**
     * @param $content
     * @return Button
     */
    public function setContent($content)
    {
        return $this->addContent($content);
    }
}