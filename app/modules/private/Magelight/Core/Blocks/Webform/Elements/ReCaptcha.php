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

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\ReCaptcha forge()
 */
class ReCaptcha extends Abstraction\Field
{
    /**
     * Captcha model
     *
     * @var \Magelight\Core\Models\Captcha\ReCaptcha
     */
    protected $_model = null;

    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'Magelight\Core\templates\webform\elements\re-captcha.phtml';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->_model = \Magelight\Core\Models\Captcha\ReCaptcha::forge();
    }

    /**
     * Get captcha HTML code
     *
     * @return string
     */
    public function getCaptchaHtml()
    {
        return $this->_model->recaptchaGetHtml();
    }
}
