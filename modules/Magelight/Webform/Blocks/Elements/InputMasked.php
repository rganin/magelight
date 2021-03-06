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
 * @method $this setName($name)
 */
class InputMasked extends Abstraction\Field
{
    /**
     * Forgery constructor (adds masked input JS to document head)
     */
    public function __forge()
    {
        \Magelight\Core\Blocks\Document::getInstance()->addJs(
            'Magelight/Webform/static/js/jquery.maskedinput.js',
            'Magelight/Core/static/js/jquery.js'
        );
        $this->addClass('masked-input');
        $this->setAttribute('type', 'text');
        $this->addClass('form-control');
    }

    /**
     * Set masked input mask
     *
     * @param string $mask
     * @return InputMasked
     */
    public function setMask($mask)
    {
        return $this->setAttribute('data-mask', $mask);
    }
}
