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
 * @method static $this forge($renderUrl = null)
 */
class Captcha extends Abstraction\Field
{
    /**
     * @var string
     */
    protected $template = 'Magelight/Webform/templates/webform/elements/captcha.phtml';

    /**
     * @var \Magelight\Webform\Models\Captcha\Captcha
     */
    protected $captcha;

    /**
     * Forgery constructor
     *
     * @param string $renderUrl
     */
    public function __forge($renderUrl = null)
    {
        $this->captcha = \Magelight\Webform\Models\Captcha\Captcha::forge();

        if (empty($renderUrl)) {
            $this->captcha->loadCodeFromSession()->generate()->saveCodeToSession();
            $this->captcha->save();
        }
        $this->set('image_url', !empty($renderUrl) ? $renderUrl : $this->url($this->captcha->getSavedFileName()));
        $this->addClass('form-group');
    }
}
