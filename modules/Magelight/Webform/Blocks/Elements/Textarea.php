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
 */
class Textarea extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'textarea';

    /**
     * Forgery
     */
    public function __forge()
    {
        $this->addClass('form-control');
    }

    /**
     * Set textarea content
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->content = [$value];
        return $this;
    }

    /**
     * Get Textarea value
     *
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->content[0]) ? $this->content[0] : '';
    }

    /**
     * Set maximum length for textarea
     *
     * @param string $maxLength
     * @return $this
     */
    public function setMaxLength($maxLength)
    {
        return $this->setAttribute('maxlength', $maxLength);
    }
}
