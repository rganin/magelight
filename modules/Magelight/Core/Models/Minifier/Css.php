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

namespace Magelight\Core\Models\Minifier;
/**
 * Class Css
 *
 * @package Magelight\Core\Models\Minifier
 */
class Css implements MinifierInterface
{
    /**
     * Minify CSS
     *
     * @param $css
     *
     * @return mixed
     */
    public function minify($css) {
        $css = preg_replace(
            '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '',
            $css
        );
        $css = str_replace(
            ["\r\n", "\r", "\n", "\t", '  ', '	', '	'], '',
            $css
        );
        $css = str_replace(
            [" {", "{ ", "; ", ": ", " :", " ,", ", ", ";}"],
            ["{", "{", ";", ":", ":", ",", ",", "}"], $css
        );
        return $css;
    }
}
