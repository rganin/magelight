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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static \Magelight\Webform\Blocks\Elements\Input forge()
 */
class Input extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'input';

    /**
     * Is element empty flag
     *
     * @var bool
     */
    protected $_empty = true;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setType('text');
        $this->addClass('form-control');
    }

    /**
     * Set input type
     *
     * @param string $type
     * @return Input
     */
    public function setType($type)
    {
        return $this->setAttribute('type', $type);
    }

    /**
     * Set input disabled flag
     *
     * @return Input
     */
    public function setDisabled()
    {
        return $this->setAttribute('disabled', 'disabled');
    }

    /**
     * Set maximum length for input
     *
     * @param string $maxLength
     * @return Input
     */
    public function setMaxLength($maxLength)
    {
        return $this->setAttribute('maxlength', $maxLength);
    }
}
