<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:55
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation\Rules;

class ReCaptcha extends AbstractRule
{

    /**
     * Validation error pattern
     *
     * @var string
     */
    protected $_error = 'Enered captcha text is invalid';

    /**
     * Fron validator (jQueryValidator) rule name
     *
     * @var string
     */
    protected $_frontValidatorRule = 'reCaptcha';

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
}
