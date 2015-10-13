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
 * @method static \Magelight\Webform\Blocks\Elements\ReCaptcha forge()
 */
class ReCaptcha extends Abstraction\Field
{
    /**
     * Captcha model
     *
     * @var \Magelight\Webform\Models\Captcha\ReCaptcha
     */
    protected $_model = null;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->_model = \Magelight\Webform\Models\Captcha\ReCaptcha::forge();
    }

    /**
     * Get captcha HTML code
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->_model->recaptchaGetHtml();
    }
}