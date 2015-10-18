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

namespace Magelight\Webform\Models\Validation\Rules;

class ReCaptcha extends AbstractRule
{
    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $frontValidatorRule = 'reCaptcha';

    /**
     * Check value with rule
     * Returns:
     *    - true if rule passed.
     *    - false if value doesn`t match the rule.
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value)
    {
        $reCaptcha = \Magelight\Webform\Models\Captcha\ReCaptcha::forge();
        $request   = \Magelight\Http\Request::getInstance();
        $challenge = $request->getPost(\Magelight\Webform\Models\Captcha\ReCaptcha::CHALLENGE_INDEX);
        $response  = $request->getPost(\Magelight\Webform\Models\Captcha\ReCaptcha::RESPONSE_INDEX);
        return $reCaptcha->recaptchaCheckAnswer($_SERVER['HTTP_HOST'], $challenge, $response)->is_valid;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        if (!empty($this->error)) {
            return $this->error;
        }
        return __('Enered reCaptcha text is invalid', $this->getErrorArguments());
    }
}
