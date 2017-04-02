<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static $this forge()
 * @method \Magelight\Webform\Blocks\Elements\Button addClass($content)
 */
class Button extends Abstraction\Element
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'button';

    /**
     * Forgery constructor
     */
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
