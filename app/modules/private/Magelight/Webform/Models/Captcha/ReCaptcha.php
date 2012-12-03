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

namespace Magelight\Webform\Models\Captcha;

/**
 * @method static \Magelight\Webform\Models\Captcha\ReCaptcha forge()
 */
class ReCaptcha
{
    use \Magelight\Forgery;
    /**
     * Field indexes
     */
    const CHALLENGE_INDEX   = 'recaptcha_challenge_field';
    const RESPONSE_INDEX    = 'recaptcha_response_field';

    /**
     * ReCaptcha service constants
     */
    const RECAPTCHA_API_SERVER        = 'http://www.google.com/recaptcha/api';
    const RECAPTCHA_API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
    const RECAPTCHA_VERIFY_SERVER     = 'www.google.com';

    /**
     * Private captcha key
     *
     * @var string
     */
    protected $_privateKey = '';

    /**
     * Public captcha key
     *
     * @var string
     */
    protected $_publicKey  = '';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setPrivateKey((string)\Magelight::app()->getConfig('global/document/re_captcha/private_key'));
        $this->setPublicKey((string)\Magelight::app()->getConfig('global/document/re_captcha/public_key'));
    }

    /**
     * Set API private key
     *
     * @param $key
     * @return ReCaptcha
     */
    public function setPrivateKey($key)
    {
        $this->_privateKey = $key;
    }

    /**
     * Set API public key
     *
     * @param $key
     * @return ReCaptcha
     */
    public function setPublicKey($key)
    {
        return $this->_publicKey = $key;
    }

    /**
     * Trigger Key error
     *
     * @throws \Magelight\Exception
     */
    protected function triggerKeyError()
    {
        throw new \Magelight\Exception(
            'To use reCAPTCHA you must get an API key from
                <a href="https://www.google.com/recaptcha/admin/create">
                    https://www.google.com/recaptcha/admin/create
                </a>',
            E_USER_WARNING
        );
    }

    /**
     * Encodes the given data into a query string format
     *
     * @param array $data
     * @return string
     */
    protected function _recaptchaQsEncode($data)
    {
        $req = '';
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }
        return substr($req, 0, strlen($req) - 1);
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     *
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int $port
     * @return array
     * @throws \Magelight\Exception
     */
    protected function _recaptchaHttpPost($host, $path, $data, $port = 80)
    {
        $req = $this->_recaptchaQsEncode ($data);
        $httpRequest  = "POST $path HTTP/1.0\r\n";
        $httpRequest .= "Host: $host\r\n";
        $httpRequest .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $httpRequest .= "Content-Length: " . strlen($req) . "\r\n";
        $httpRequest .= "User-Agent: reCAPTCHA/PHP\r\n";
        $httpRequest .= "\r\n";
        $httpRequest .= $req;
        $response = '';
        if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 2))) {
            throw new \Magelight\Exception('Could not open socket for reCaptcha', E_USER_WARNING);
        }
        fwrite($fs, $httpRequest);
        while (!feof($fs)) {
            $response .= fgets($fs, 1160);
        }
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);
        return $response;
    }

    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * This is called from the browser, and the resulting reCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     *
     * @param string $error
     * @param bool $useSsl
     * @return string
     */
    public function recaptchaGetHtml($error = null, $useSsl = false)
    {
        if (empty($this->_publicKey)) {
            $this->triggerKeyError();
        }
        if ($useSsl) {
            $server = self::RECAPTCHA_API_SECURE_SERVER;
        } else {
            $server = self::RECAPTCHA_API_SERVER;
        }
        $errorPart = "";
        if ($error) {
            $errorPart = "&amp;error=" . $error;
        }
        return '<script type="text/javascript" src="'
            . $server
            . '/challenge?k=' . $this->_publicKey . $errorPart . '"></script>
        <noscript>
            <iframe src="'
            . $server
            . '/noscript?k='
            . $this->_publicKey
            . $errorPart
            . '" height="300" width="500" frameborder="0"></iframe><br/>
            <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
            <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
        </noscript>';
    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct
     *
     * @param string $remoteip
     * @param string $challenge
     * @param string $response
     * @param array $extraParams
     * @return ReCaptchaResponse
     * @throws \Magelight\Exception
     */
    public function recaptchaCheckAnswer($remoteip, $challenge, $response, $extraParams = [])
    {
        if (empty($this->_privateKey)) {
            $this->triggerKeyError();
        }
        if ($remoteip == null || $remoteip == '') {
            throw new \Magelight\Exception(
                "For security reasons, you must pass the remote ip to reCAPTCHA"
            );
        }
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
            $recaptchaResponse = new ReCaptchaResponse();
            $recaptchaResponse->is_valid = false;
            $recaptchaResponse->error = 'incorrect-captcha-sol';
            return $recaptchaResponse;
        }
        $response = $this->_recaptchaHttpPost(self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify",
            [
                'privatekey' => $this->_privateKey,
                'remoteip' => $remoteip,
                'challenge' => $challenge,
                'response' => $response
            ] + $extraParams
        );
        $answers = explode ("\n", $response [1]);
        $recaptchaResponse = new ReCaptchaResponse();
        if (trim ($answers [0]) == 'true') {
            $recaptchaResponse->is_valid = true;
        }
        else {
            $recaptchaResponse->is_valid = false;
            $recaptchaResponse->error = $answers [1];
        }
        return $recaptchaResponse;

    }

    /**
     * Gets a URL where the user can sign up for reCAPTCHA. If your application
     * has a configuration page where you enter a key, you should provide a link
     * using this function.
     *
     * @param string $domain
     * @param string $appname
     * @return string
     */
    public function recaptchaGetSignupUrl($domain = null, $appname = null)
    {
        return "https://www.google.com/recaptcha/admin/create?" .  $this->_recaptchaQsEncode(
            ['domains' => $domain, 'app' => $appname]
        );
    }

    /**
     * Pad AES encryption block
     *
     * @param string $val
     * @return string
     */
    protected function _recaptchaAesPad($val)
    {
        $block_size = 16;
        $numpad = $block_size - (strlen($val) % $block_size);
        return str_pad($val, strlen($val) + $numpad, chr($numpad));
    }

    /**
     * Encrypt value with AES by key
     *
     * @param string $val
     * @param string $ky
     * @return string
     * @throws \Magelight\Exception
     */
    protected function _recaptchaAesEncrypt($val, $ky)
    {
        if (!function_exists ("mcrypt_encrypt")) {
            throw new \Magelight\Exception(
                'To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.'
            );
        }
        $mode = MCRYPT_MODE_CBC;
        $enc  = MCRYPT_RIJNDAEL_128;
        $val  = $this->_recaptchaAesPad($val);
        return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    /**
     * Gets the reCAPTCHA Mailhide url for a given email, public key and private key
     *
     * @param string $x
     * @return string
     */
    protected function _recaptchaMailhideUrlBase64($x)
    {
        return strtr(base64_encode ($x), '+/', '-_');
    }

    /**
     * Gets the reCAPTCHA Mailhide url for a given email, public key and private key
     *
     * @param string $email
     * @return string
     * @throws \Magelight\Exception
     */
    public function recaptchaMailhideUrl($email)
    {
        if (empty($this->_publicKey) || empty($this->_privateKey)) {
            throw new \Magelight\Exception(
                "To use reCAPTCHA Mailhide, you have to sign up for a public and private key, "
                    . "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'> "
                    . "http://www.google.com/recaptcha/mailhide/apikey</a>"
            );
        }
        $ky = pack('H*', $this->_privateKey);
        $cryptmail = $this->_recaptchaAesEncrypt($email, $ky);
        return "http://www.google.com/recaptcha/mailhide/d?k="
            . $this->_publicKey
            . "&c="
            . $this->_recaptchaMailhideUrlBase64($cryptmail);
    }

    /**
     * Gets the parts of the email to expose to the user. Eg, given johndoe@example.com return ["john", "example.com"].
     * The email is then displayed as john...@example.com
     *
     * @param string $email
     * @return array
     */
    protected function _recaptchaMailhideEmailParts($email)
    {
        $arr = preg_split("/@/", $email );
        if (strlen ($arr[0]) <= 4) {
            $arr[0] = substr ($arr[0], 0, 1);
        } else if (strlen ($arr[0]) <= 6) {
            $arr[0] = substr ($arr[0], 0, 3);
        } else {
            $arr[0] = substr ($arr[0], 0, 4);
        }
        return $arr;
    }

    /**
     * Gets html to display an email address given a public an private key.
     * To get a key visit: http://www.google.com/recaptcha/mailhide/apikey
     *
     * @param string $email
     * @return string
     */
    public function recaptchaMailhideHtml($email)
    {
        $emailParts = $this->_recaptchaMailhideEmailParts($email);
        $url = $this->recaptchaMailhideUrl($email);
        return htmlentities($emailParts[0])
            . "<a href='" . htmlentities ($url)
            . "' onclick=\"window.open('" . htmlentities ($url)
            . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300');"
            . "return false;\" title=\"Reveal this e-mail address\">...</a>@"
            . htmlentities ($emailParts[1]);

    }
}

/**
 * Captcha response class
 */
class ReCaptchaResponse {

    /**
     * Is result valid
     *
     * @var bool
     */
    public $is_valid;

    /**
     * Error string
     *
     * @var string
     */
    public $error;
}
