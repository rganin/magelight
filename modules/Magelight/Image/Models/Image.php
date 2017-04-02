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

namespace Magelight\Image\Models;

/**
 * @method static $this forge($filename)
 */
class Image
{
    /**
     * Orientation constants
     */
    const ORIENTATION_PORTRAIT          = 'portrait';
    const ORIENTATION_LANDSCAPE         = 'landscape';
    const ORIENTATION_SQUARE            = 'square';

    /**
     * Flip direction constants
     */
    const FLIP_DIRECTION_HORIZONTAL     = 'x';
    const FLIP_DIRECTION_VERTICAL       = 'y';

    /**
     * Color constants
     */
    const BG_COLOR_BLACK                = '#000000';
    const BG_COLOR_WHITE                = '#FFFFFF';

    /**
     * Blur type constants
     */
    const BLUR_TYPE_SELECTIVE           = 'selective';
    const BLUR_TYPE_GAUSSIAN            = 'gaussian';

    /**
     * Overlay position constants
     */
    const OVERLAY_POSITION_TOPLEFT      = 'top left';
    const OVERLAY_POSITION_TOPRIGHT     = 'top right';
    const OVERLAY_POSITION_TOP          = 'top';
    const OVERLAY_POSITION_BOTTOMLEFT   = 'bottom left';
    const OVERLAY_POSITION_BOTTOMRIGHT  = 'bottom right';
    const OVERLAY_POSITION_BOTTOM       = 'bottom';
    const OVERLAY_POSITION_LEFT         = 'left';
    const OVERLAY_POSITION_RIGHT        = 'right';
    const OVERLAY_POSITION_CENTER       = 'center';

    /**
     * Mime type constants
     */
    const TYPE_GIF  = 'image/gif';
    const TYPE_JPEG = 'image/jpeg';
    const TYPE_PNG  = 'image/png';

    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Cache TTL for client
     *
     * @var int
     */
    protected $clientCacheTtl = 864000;

    /**
     * Image resource
     *
     * @var resource
     */
    protected $image = null;

    /**
     * Image filename
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Original image info
     *
     * @var array
     */
    protected $originalInfo = null;

    /**
     * Image width
     *
     * @var int
     */
    protected $width = 0;

    /**
     * Image height
     *
     * @var int
     */
    protected $height = 0;

    /**
     * Exif image information
     *
     * @var array
     */
    protected $exif = [];

    /**
     * Image type
     *
     * @var null
     */
    protected $type = null;

    /**
     * Forgery constructor
     *
     * @param string $filename
     */
    public function __forge($filename)
    {
        if (!empty($filename) && is_readable($filename)) {
            $this->load($filename);
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }

    /**
     * Load image from file
     *
     * @param string $filename
     * @return Image
     * @throws \Magelight\Exception
     */
    public function load($filename) {

        if (!extension_loaded('gd')) {
            throw new \Magelight\Exception('Required extension GD is not loaded.');
        }
        $this->filename = $filename;
        $info = getimagesize($this->filename);
        $this->type = $info['mime'];
        switch ($info['mime']) {
            case self::TYPE_GIF:
                $this->image = imagecreatefromgif($this->filename);
                break;
            case self::TYPE_JPEG:
                $this->image = imagecreatefromjpeg($this->filename);
                break;
            case self::TYPE_PNG:
                $this->image = imagecreatefrompng($this->filename);
                break;
            default:
                throw new \Magelight\Exception('Invalid image: ' . $this->filename);
                break;
        }
        $this->originalInfo = [
            'width' => $info[0],
            'height' => $info[1],
            'orientation' => $this->getOrientation(),
            'exif' => function_exists('exif_read_data') ? $this->exif = @exif_read_data($this->filename) : null,
            'format' => preg_replace('/^image\//', '', $info['mime']),
            'mime' => $info['mime']
        ];
        $this->width = $info[0];
        $this->height = $info[1];
        imagesavealpha($this->image, true);
        imagealphablending($this->image, true);
        return $this;
    }

    /**
     * Save image to file
     *
     * @param string $filename
     * @param int $quality - 0-100 for jpeg, 0-9 for png
     * @return Image
     * @throws \Magelight\Exception
     */
    public function save($filename = null, $quality = null) {
        if (!$filename) {
            $filename = $this->filename;
        }
        $format = $this->fileExt($filename);
        if (!$format) {
            $format = $this->originalInfo['format'];
        }
        $format = strtolower($format);
        switch( $format ) {
            case 'gif':
                $result = imagegif($this->image, $filename);
                break;
            case 'jpg':
            case 'jpeg':
                if( $quality === null ) $quality = 85;
                $quality = $this->keepWithin($quality, 0, 100);
                $result = imagejpeg($this->image, $filename, $quality);
                break;
            case 'png':
                if( $quality === null ) $quality = 9;
                $quality = $this->keepWithin($quality, 0, 9);
                $result = imagepng($this->image, $filename, $quality);
                break;
            default:
                throw new \Magelight\Exception('Unsupported format');
        }
        if (!$result) {
            throw new \Magelight\Exception('Unable to save image: ' . $filename);
        }
        return $this;
    }

    /**
     * Get original info
     * Result:
     * array(
     *		width => 320,
     *		height => 200,
     *		orientation => ['portrait', 'landscape', 'square'],
     *		exif => array(...),
     *		mime => ['image/jpeg', 'image/gif', 'image/png'],
     *		format => ['jpeg', 'gif', 'png']
     *	)
     *
     * @return array|null
     */
    public function getOriginalInfo() {
        return $this->originalInfo;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth() {
        return imagesx($this->image);
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function getHeight() {
        return imagesy($this->image);
    }

    /**
     * Get image orientation
     *
     * @return string
     */
    public function getOrientation() {

        if (imagesx($this->image) > imagesy($this->image)) {
            return self::ORIENTATION_LANDSCAPE;
        }
        if (imagesx($this->image) < imagesy($this->image)) {
            return self::ORIENTATION_PORTRAIT;
        }
        return self::ORIENTATION_SQUARE;
    }

    /**
     * Flip image
     *
     * @param string $direction
     * @return Image
     */
    public function flip($direction = self::FLIP_DIRECTION_HORIZONTAL) {
        $new = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($new, false);
        imagesavealpha($new, true);
        switch (strtolower($direction)) {
            case self::FLIP_DIRECTION_VERTICAL:
                for ($y = 0; $y < $this->height; $y++) {
                    imagecopy($new, $this->image, 0, $y, 0, $this->height - $y - 1, $this->width, 1);
                }
                break;
            default:
                for ($x = 0; $x < $this->width; $x++) {
                    imagecopy($new, $this->image, $x, 0, $this->width - $x - 1, 0, 1, $this->height);
                }
                break;
        }
        $this->image = $new;
        return $this;
    }

    /**
     * Rotate image
     *
     * @param int $angle - 0-360
     * @param string $bgColor - hex color, e.g. #013ffa
     * @return Image
     */
    public function rotate($angle, $bgColor = self::BG_COLOR_BLACK) {
        $rgb = $this->hex2rgb($bgColor);
        $bgColor = imagecolorallocate($this->image, $rgb['r'], $rgb['g'], $rgb['b']);
        $new = imagerotate($this->image, -($this->keepWithin($angle, -360, 360)), $bgColor);
        imagesavealpha($new, true);
        imagealphablending($new, true);
        $this->width = imagesx($new);
        $this->height = imagesy($new);
        $this->image = $new;
        return $this;
    }

    /**
     * Rotates image to set correct orientation according to exif info
     *
     * @return Image
     */
    public function autoOrient() {
        switch( $this->originalInfo['exif']['Orientation'] ) {
            case 1:
                break;
            case 2:
                $this->flip(self::FLIP_DIRECTION_HORIZONTAL);
                break;
            case 3:
                $this->rotate(-180);
                break;
            case 4:
                $this->flip(self::FLIP_DIRECTION_VERTICAL);
                break;
            case 5:
                $this->flip(self::FLIP_DIRECTION_VERTICAL);
                $this->rotate(90);
                break;
            case 6:
                $this->rotate(90);
                break;
            case 7:
                $this->flip(self::FLIP_DIRECTION_HORIZONTAL);
                $this->rotate(90);
                break;
            case 8:
                $this->rotate(-90);
                break;
        }
        return $this;
    }

    /**
     * Resize image
     *
     * @param int $width
     * @param int $height
     * @return Image
     */
    public function resize($width, $height)
    {
        $new = imagecreatetruecolor($width, $height);
        imagealphablending($new, false);
        imagesavealpha($new, true);
        imagecopyresampled($new, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        $this->width = $width;
        $this->height = $height;
        $this->image = $new;
        return $this;

    }

    /**
     * Fit image to width
     *
     * @param int $width
     * @return Image
     */
    public function fitToWidth($width) {
        $aspectRatio = $this->height / $this->width;
        $height = $width * $aspectRatio;
        return $this->resize($width, $height);
    }

    /**
     * Fit image to height
     *
     * @param string $height
     * @return Image
     */
    public function fitToHeight($height) {
        $aspectRatio = $this->height / $this->width;
        $width = $height / $aspectRatio;
        return $this->resize($width, $height);
    }

    /**
     * Best fit image to width and height
     *
     * @param int $maxWidth
     * @param int $maxHeight
     * @return Image
     */
    public function fitBest($maxWidth, $maxHeight) {
        if ($this->width <= $maxWidth && $this->height <= $maxHeight) {
            return $this;
        }
        $aspectRatio = $this->height / $this->width;
        if( $this->width > $maxWidth ) {
            $width = $maxWidth;
            $height = $width * $aspectRatio;
        } else {
            $width = $this->width;
            $height = $this->height;
        }
        if( $height > $maxHeight ) {
            $height = $maxHeight;
            $width = $height / $aspectRatio;
        }
        return $this->resize($width, $height);
    }

    /**
     * Crop image
     *
     * @param int $left
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @return Image
     */
    public function crop($left, $top, $right, $bottom) {
        if( $right < $left ) list($left, $right) = [$right, $left];
        if( $bottom < $top ) list($top, $bottom) = [$bottom, $top];
        $cropWidth = $right - $left;
        $cropHeight = $bottom - $top;
        $new = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($new, false);
        imagesavealpha($new, true);
        imagecopyresampled($new, $this->image, 0, 0, $left, $top, $cropWidth, $cropHeight, $cropWidth, $cropHeight);
        $this->width = $cropWidth;
        $this->height = $cropHeight;
        $this->image = $new;
        return $this;
    }

    /**
     * Square crop
     *
     * @param int $size - default minimal side (width or height)
     * @return Image
     */
    public function cropSquare($size = null) {
        if( $this->width > $this->height ) {
            $x_offset = ($this->width - $this->height) / 2;
            $y_offset = 0;
            $square_size = $this->width - ($x_offset * 2);
        } else {
            $x_offset = 0;
            $y_offset = ($this->height - $this->width) / 2;
            $square_size = $this->height - ($y_offset * 2);
        }
        $this->crop($x_offset, $y_offset, $x_offset + $square_size, $y_offset + $square_size);
        if( $size ) $this->resize($size, $size);
        return $this;
    }

    /**
     * Desaturate image
     *
     * @return Image
     */
    public function desaturate() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Invert image colors
     *
     * @return Image
     */
    public function invert() {
        imagefilter($this->image, IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * Adjust brightness
     *
     * @param int $level - darkest = -255, lightest = 255 (required)
     * @return Image
     */
    public function brightness($level) {
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $this->keepWithin($level, -255, 255));
        return $this;
    }

    /**
     * Adjust contrast
     *
     * @param int $level - min = -100, max, 100 (required)
     * @return Image
     */
    public function contrast($level) {
        imagefilter($this->image, IMG_FILTER_CONTRAST, $this->keepWithin($level, -100, 100));
        return $this;
    }

    /**
     * Colorize image
     *
     * @param string $color - any valid hex color (required)
     * @param int $opacity - 0 | 1
     * @return Image
     */
    public function colorize($color, $opacity) {
        $rgb = $this->hex2rgb($color);
        $alpha = $this->keepWithin(127 - (127 * $opacity), 0, 127);
        imagefilter(
            $this->image,
            IMG_FILTER_COLORIZE,
            $this->keepWithin($rgb['r'], 0, 255),
            $this->keepWithin($rgb['g'], 0, 255),
            $this->keepWithin($rgb['b'], 0, 255),
            $alpha
        );
        return $this;
    }

    /**
     * Edge detect filter
     *
     * @return Image
     */
    public function edges() {
        imagefilter($this->image, IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * Emboss image
     *
     * @return Image
     */
    public function emboss() {
        imagefilter($this->image, IMG_FILTER_EMBOSS);
        return $this;
    }

    /**
     * Mean remove effect
     *
     * @return Image
     */
    public function meanRemove() {
        imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }



    /**
     * Number of time
     *
     * @param string $type - use this class constants
     * @param int $passes - number of time for filter to be applied
     * @return Image
     */
    public function blur($type = self::BLUR_TYPE_SELECTIVE, $passes = 1) {

        switch( strtolower($type) ) {
            case self::BLUR_TYPE_GAUSSIAN:
                $type = IMG_FILTER_GAUSSIAN_BLUR;
                break;
            default:
                $type = IMG_FILTER_SELECTIVE_BLUR;
                break;
        }
        for ($i = 0; $i < $passes; $i++) {
            imagefilter($this->image, $type);
        }
        return $this;

    }

    /**
     * Sketch filter
     *
     * @return Image
     */
    public function sketch() {
        imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }

    /**
     * Smooth image
     *
     * @param $level - [-10; 10]
     * @return Image
     */
    public function smooth($level) {
        imagefilter($this->image, IMG_FILTER_SMOOTH, $this->keepWithin($level, -10, 10));
        return $this;
    }

    /**
     * Pixelate image
     *
     * @param int $blockSize
     * @return Image
     */
    public function pixelate($blockSize = 10) {
        imagefilter($this->image, IMG_FILTER_PIXELATE, $blockSize, true);
        return $this;
    }

    /**
     * Apply Grayscale filter
     *
     * @return Image
     */
    public function grayscale() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Apply sepia filter
     *
     * @return Image
     */
    public function sepia() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 50, 0);
        return $this;
    }

    /**
     * Add image overlay (watermark)
     *
     * @param string $overlayFile
     * @param string $position
     * @param int $opacity - opacity in percents
     * @param int $x_offset
     * @param int $y_offset
     * @return Image
     */
    public function overlay($overlayFile,
                            $position = self::OVERLAY_POSITION_TOPLEFT,
                            $opacity = 1,
                            $x_offset = 0,
                            $y_offset = 0
    )
    {
        $overlay = static::forge($overlayFile);
        $opacity = $opacity * 100;
        switch( strtolower($position) ) {
            case self::OVERLAY_POSITION_TOPLEFT:
                $x = 0 + $x_offset;
                $y = 0 + $y_offset;
                break;
            case self::OVERLAY_POSITION_TOPRIGHT:
                $x = $this->width - $overlay->width + $x_offset;
                $y = 0 + $y_offset;
                break;
            case self::OVERLAY_POSITION_TOP:
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = 0 + $y_offset;
                break;
            case self::OVERLAY_POSITION_BOTTOMLEFT:
                $x = 0 + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case self::OVERLAY_POSITION_BOTTOMRIGHT:
                $x = $this->width - $overlay->width + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case self::OVERLAY_POSITION_BOTTOM:
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case self::OVERLAY_POSITION_LEFT:
                $x = 0 + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
            case self::OVERLAY_POSITION_RIGHT:
                $x = $this->width - $overlay->width + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
            case self::OVERLAY_POSITION_CENTER:
            default:
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
        }
        $this->imageCopyMergeAlpha(
            $this->image,
            $overlay->image, $x, $y, 0, 0, $overlay->width, $overlay->height, $opacity
        );
        return $this;
    }

    /**
     * Add text to image
     *
     * @param string $text
     * @param string $font_file
     * @param string $font_size
     * @param string $color
     * @param string $position
     * @param int $x_offset
     * @param int $y_offset
     * @return Image
     * @throws \Magelight\Exception
     */
    public function text($text,
                         $font_file,
                         $font_size = '12',
                         $color = '#000000',
                         $position = self::OVERLAY_POSITION_BOTTOMRIGHT,
                         $x_offset = 0,
                         $y_offset = 0
    )
    {
        $angle = 0;
        $rgb = $this->hex2rgb($color);
        $color = imagecolorallocate($this->image, $rgb['r'], $rgb['g'], $rgb['b']);
        $box = imagettfbbox($font_size, $angle, $font_file, $text);
        if( !$box ) throw new \Magelight\Exception('Unable to load font: ' . $font_file);
        $box_width = abs($box[6] - $box[2]);
        $box_height = abs($box[7] - $box[1]);
        switch( strtolower($position) ) {
            case self::OVERLAY_POSITION_TOPLEFT:
                $x = 0 + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_TOPRIGHT:
                $x = $this->width - $box_width + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_TOP:
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_BOTTOMLEFT:
                $x = 0 + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_BOTTOMRIGHT:
                $x = $this->width - $box_width + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_BOTTOM:
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case self::OVERLAY_POSITION_LEFT:
                $x = 0 + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;
            case self::OVERLAY_POSITION_RIGHT;
                $x = $this->width - $box_width + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;
            case self::OVERLAY_POSITION_CENTER:
            default:
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;

        }
        imagettftext($this->image, $font_size, $angle, $x, $y, $color, $font_file, $text);
        return $this;
    }

    /**
     * Copymerge image
     *
     * @param resource $dst_im
     * @param resource $src_im
     * @param int $dst_x
     * @param int $dst_y
     * @param int $src_x
     * @param int $src_y
     * @param int $src_w
     * @param int $src_h
     * @param int $pct
     * @return bool|Image
     */
    private function imageCopyMergeAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ) {
        $pct /= 100;
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        imagealphablending($src_im, false);
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                // Calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                // Get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha(
                    $src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha
                );
                // Set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        return $this;
    }

    /**
     * Keep value within bounds
     *
     * @param int $value
     * @param int $min
     * @param int $max
     * @return mixed
     */
    private function keepWithin($value, $min, $max) {
        if ($value < $min) {
            return $min;
        }
        if ($value > $max) {
            return $max;
        }
        return $value;
    }

    /**
     * Get file extension
     *
     * @param string $filename
     * @return mixed|string
     */
    private function fileExt($filename) {
        if (!preg_match('/\./', $filename)) {
            return '';
        }
        return preg_replace('/^.*\./', '', $filename);
    }

    /**
     * Convert hex color to RBG array
     *
     * @param string $hex_color
     * @return array|bool
     */
    private function hex2rgb($hex_color) {
        if ($hex_color[0] == '#') {
            $hex_color = substr($hex_color, 1);
        }
        if (strlen($hex_color) == 6) {
            list ($r, $g, $b) = [
                $hex_color[0] . $hex_color[1],
                $hex_color[2] . $hex_color[3],
                $hex_color[4] . $hex_color[5]
            ];
        } elseif (strlen($hex_color) == 3) {
            list ($r, $g, $b) = [
                $hex_color[0] . $hex_color[0],
                $hex_color[1] . $hex_color[1],
                $hex_color[2] . $hex_color[2]
            ];
        } else {
            return false;
        }
        return [
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        ];
    }

    /**
     * Sender image to response
     *
     * @param \Magelight\Http\Response $response
     */
    public function render(\Magelight\Http\Response $response)
    {
        $ts = gmdate("D, d M Y H:i:s", time() + $this->clientCacheTtl) . " GMT";
        $response->addHeader('Content-Type', $this->type);
        $response->addHeader('Expires', $ts);
        $response->addHeader('Pragma', 'cache');
        $response->addHeader('Cache-Control', 'max-age=' . $this->clientCacheTtl);

        ob_start();
        switch ($this->type) {
            case self::TYPE_GIF:
                imagegif($this->image);
                break;
            case self::TYPE_JPEG:
                imagejpeg($this->image);
                break;
            case self::TYPE_PNG:
                imagepng($this->image);
                break;
            default:
                break;
        }
        $response->setContent(ob_get_clean());
        $response->send();
    }
}
