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

namespace Magelight\Helpers;

/**
 * @method static \Magelight\Helpers\ColorHelper forge()
 */
class ColorHelper
{
    use \Magelight\Traits\TForgery;

    /**
     * Css color
     *
     * @var array
     */
    protected $cssColors = [
        'maroon' => [
            'css' => '#800000',
            'rgb' => [128,0,0],
        ],
        'red' => [
            'css' => '#ff0000',
            'rgb' => [255,0,0],
        ],
        'yellow' => [
            'css' => '#ffff00',
            'rgb' => [255,255,0],
        ],
        'olive' => [
            'css' => '#808000',
            'rgb' => [128,128,0],
        ],
        'purple' => [
            'css' => '#800080',
            'rgb' => [128,0,128],
        ],
        'fuchsia' => [
            'css' => '#ff00ff',
            'rgb' => [255,0,255],
        ],
        'white' => [
            'css' => '#ffffff',
            'rgb' => [255,255,255],
        ],
        'lime' => [
            'css' => '#00ff00',
            'rgb' => [0,255,0],
        ],
        'green' => [
            'css' => '#008000',
            'rgb' => [0,128,0],
        ],
        'navy' => [
            'css' => '#000080',
            'rgb' => [0,0,128],
        ],
        'blue' => [
            'css' => '#0000ff',
            'rgb' => [0,0,255],
        ],
        'aqua' => [
            'css' => '#0000ff',
            'rgb' => [0,255,255],
        ],
        'teal' => [
            'css' => '#008080',
            'rgb' => [0,128,120],
        ],
        'black' => [
            'css' => '#000000',
            'rgb' => [0,0,0],
        ],
        'silver' => [
            'css' => '#c0c0c0',
            'rgb' => [192,192,192],
        ],
        'gray' => [
            'css' => '#808080',
            'rgb' => [128,128,128],
        ],
        'orange' => [
            'css' => '#ffa500',
            'rgb' => [255,165,0],
        ],
    ];

    /**
     * Convert css color to RGB array
     *
     * @param string $cssColor
     * @return array
     */
    public function cssToRgb($cssColor = '#000000')
    {
        $cssColor = strtolower($cssColor);
        if (isset($this->cssColors[$cssColor])) {
            return $this->cssColors[$cssColor]['rgb'];
        }
        $cssColor = str_replace('#', '', $cssColor);
        if (strlen($cssColor) < 4) {
            $cssColor = preg_replace('/([a-f0-9]+)/i', '\\1\\1', $cssColor);
        }
        if (strlen($cssColor) !== 6) {
            return [0,0,0];
        }
        $cssColor = str_split($cssColor, 2);
        foreach ($cssColor as $key => $value) {
            $cssColor[$key] = hexdec($value);
        }
        return $cssColor + [0,0,0];
    }

    /**
     * Convert RGB array to css color
     *
     * @param array $rgb
     * @return string
     */
    public function rgbToCss($rgb = [0,0,0])
    {
        if (count($rgb) !== 3) {
            trigger_error("Invalid color passed: " . var_export($rgb, true), E_USER_WARNING);
            $rgb = [0,0,0];
        }
        foreach ($rgb as $key => $value) {
            $rgb[$key] = dechex($value);
            if (strlen($rgb[$key]) < 2) {
                $rgb[$key] = '0' . $rgb[$key];
            }
        }
        return '#' . implode('', $rgb);
    }

    /**
     * Allocate image color from rgb array
     *
     * @param resource $image
     * @param array $color
     * @return int
     */
    public function allocateImageColor($image, $color = [0,0,0])
    {
        list ($r, $g, $b) = $color;
        return imagecolorallocate($image, $r, $g, $b);
    }

    /**
     * Allocate image color from css code
     *
     * @param resource $image
     * @param string $cssColor
     * @return int
     */
    public function allocateImageColorCss($image, $cssColor)
    {
        return $this->allocateImageColor($image, $this->cssToRgb($cssColor));
    }
}
