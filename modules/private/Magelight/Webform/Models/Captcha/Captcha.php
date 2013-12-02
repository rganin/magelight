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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Models\Captcha;

/**
 * @method static \Magelight\Webform\Models\Captcha\Captcha forge()
 * @property string $charlist
 * @property string $save_path
 * @property int $code_length
 * @property string $background_color
 * @property string $font_file
 * @property int $font_size
 * @property int $width
 * @property int $height
 * @property string $font_color
 * @property string $session_code
 * @property int $ttl
 * @property bool $case_sensitive
 */
class Captcha
{
    use \Magelight\Traits\TForgery;
    use \Magelight\Traits\TGetSet;

    /**
     * Captcha config is loaded from global/document/captcha node
     *
     * @var array
     */
    protected $_config = [
        'charlist'          => '1234567890',
        'save_path'         => null,
        'code_length'       => 5,
        'background_color'  => '#ddd',
        'font_file'         => 'Magelight/Webform/static/fonts/comic.ttf',
        'font_size'         => 18,
        'width'             => 100,
        'height'            => 32,
        'font_color'        =>'#000',
        'session_code'      => 'captcha_code',
        'ttl'               => 180,
        'case_sensitive'    => false
    ];

    /**
     * Image
     *
     * @var resource|null
     */
    protected $_image = null;

    /**
     * Captcha code
     *
     * @var null
     */
    protected $_code = null;

    /**
     * Save filename
     *
     * @var null
     */
    protected $_fileName = null;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setGetSetTarget($this->_config);
        $this->_config =
            array_merge($this->_config, (array)\Magelight::app()->config()->getConfig('global/document/captcha', []));

        $this->cleanup();
    }

    /**
     * Generate random code
     *
     * @return string
     */
    protected function generateCode()
    {
        $code = '';
        $maxIndex = strlen($this->charlist) - 1;
        for ($i = 0; $i < $this->code_length; $i++) {
            $code .= $this->charlist[rand(0, $maxIndex)];
        }
        return $code;
    }

    /**
     * Save current captcha code to session
     *
     * @return Captcha
     */
    public function saveCodeToSession()
    {
        \Magelight::app()->session()->set($this->session_code, $this->_code);
        return $this;
    }

    /**
     * Load captcha code from sesion
     *
     * @return Captcha
     */
    public function loadCodeFromSession()
    {
        $this->_code = \Magelight::app()->session()->get($this->session_code, null);
        return $this;
    }

    /**
     * Generate captcha image
     *
     * @return Captcha
     */
    public function generate()
    {
        if (!$this->_code) {
            $this->_code = $this->generateCode();
        }

        $font_size = isset($this->font_size) ? $this->font_size : $this->height * 0.75;
        $this->_image = @imagecreate($this->width, $this->height);
        if (!$this->_image) {
            trigger_error("Unable to allocate image", E_USER_WARNING);
            return $this;
        }
        $colorHelper = \Magelight\Helpers\ColorHelper::forge();

        $bgColor = $colorHelper->allocateImageColorCss($this->_image, $this->background_color);
        $textColor = $colorHelper->allocateImageColorCss($this->_image, $this->font_color);
        $noiseColor = $colorHelper->allocateImageColorCss($this->_image, $this->noise_color);

        for( $i=0; $i<($this->width * $this->height)/3; $i++ ) {
            imagefilledellipse($this->_image, mt_rand(0, $this->width), mt_rand(0,$this->height), 1, 1, $noiseColor);
        }

        for( $i=0; $i<($this->width * $this->height)/150; $i++ ) {
            imageline($this->_image,
                mt_rand(0,$this->width),
                mt_rand(0,$this->height),
                mt_rand(0,$this->width),
                mt_rand(0,$this->height),
                $noiseColor
            );
        }

        $textbox = imagettfbbox($font_size, 0, $this->font_file, $this->_code);
        $x = ($this->width - $textbox[4])/2;
        $y = ($this->height - $textbox[5])/2;
        imagettftext($this->_image, $font_size, 0, $x, $y, $textColor, $this->font_file , $this->_code);
        return $this;
    }

    /**
     * Save captcha to file
     *
     * @return Captcha
     */
    public function save()
    {
        if (isset($this->save_path)) {
            if (!is_readable($this->save_path)) {
                mkdir($this->save_path);
            }
            if (!is_writeable($this->save_path)) {
                trigger_error("Captcha save path {$this->save_path} is not writeable.", E_USER_WARNING);
            }
            $this->_fileName = trim($this->save_path, '\\/') . DS . md5($this->_code) . 'captcha.jpg';
            \Magelight::app()->cache()
                ->set(realpath(dirname($this->_fileName)) . DS . basename($this->_fileName), 1, $this->ttl);
            imagejpeg($this->_image, $this->_fileName, 75);
        }
        return $this;
    }

    /**
     * Cleanup images cache
     *
     * @return Captcha
     */
    public function cleanup()
    {
        if (isset($this->save_path)) {
            $files = glob(trim($this->save_path, '\\/') . DS . '*captcha.jpg');
            foreach ($files as $file) {
                if (!\Magelight::app()->cache()->get(realpath($file), false)) {
                    unlink($file);
                }
            }
        }
        return $this;
    }

    /**
     * Get filename captcha was saved to
     *
     * @return null
     */
    public function getSavedFileName()
    {
        return $this->_fileName;
    }

    /**
     * Render captcha image
     *
     * @return Captcha
     */
    public function render()
    {
        imagejpeg($this->_image);
        return $this;
    }

    /**
     * Check is captcha correct
     *
     * @param string $code
     * @return bool
     */
    public function check($code)
    {
        if ($this->case_sensitive) {
            return $code === \Magelight::app()->session()->get($this->session_code, null);
        }
        $check = \Magelight::app()->session()->get($this->session_code, null);
        $result = strtolower($code) == strtolower($check);
        \Magelight::app()->session()->unsetData($this->session_code);
        return $result;
    }
}
