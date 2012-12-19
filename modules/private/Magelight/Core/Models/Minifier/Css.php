<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 12.12.12
 * Time: 2:00
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Models\Minifier;

class Css implements MinifierInterface
{
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
