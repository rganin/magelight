<?php

namespace Magelight\Core\Models\Minifier;
/**
 * MinifyJS class
 *
 * This source file can be used to minify Javascript files.
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them.
 * Reporting can be done by sending an email to minify@mullie.eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * License
 * Copyright (c) 2012, Matthias Mullie. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that
 * the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 * disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 * following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to,
 * the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the
 * author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including,
 * but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business
 * interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising in any way out of the use of this software, even if advised of
 * the possibility of such damage.
 *
 * @author Matthias Mullie <minify@mullie.eu>
 * @author Tijs Verkoyen <minify@verkoyen.eu>
 * @version 1.1.0
 *
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved.
 * @license MIT License
 */

class Js implements \Magelight\Core\Models\Minifier\MinifierInterface {

    const STRIP_COMMENTS = 1;
    const STRIP_WHITESPACE = 2;
    const ALL = 2047;

    /**
     * Extract comments & strings from source code (and replace them with a placeholder)
     * This fancy parsing is neccessary because comments can contain string demarcators and vice versa, and both can
     * contain content that is very similar to the rest of the code.
     *
     * @param string $content The file/content to extract comments & strings for.
     * @return array An array containing the (manipulated) content, the strings & the comments.
     */
    protected function extract($content)
    {
        $strings = [];
        $comments = [];
        for ($i = 0; $i < strlen($content); $i++) {
            $character = $content[$i];
            switch ($content[$i]) {
                case '\'':
                case '"':
                    $stringOpener = $character;
                    for($j = $i + 1; $j < strlen($content); $j++) {
                        $character = $content[$j];
                        $previousCharacter = isset($content[$j - 1]) ? $content[$j - 1] : '';
                        if (
                            ($stringOpener == $character && $previousCharacter != '\\')
                            ||
                            (in_array($character, array("\r", "\n")) && $previousCharacter != '\\')
                        ) {
                            $replacement = '[MINIFY-STRING-' . count($strings) . ']';
                            $strings[$replacement] = substr($content, $i, $j - $i + 1);
                            $content = substr_replace($content, $replacement, $i, $j - $i + 1);
                            $i += strlen($replacement);
                            break;
                        }
                    }
                    break;
                case '/':
                    $commentOpener = $character . (isset($content[$i + 1]) ? $content[$i + 1] : '');
                    if (in_array($commentOpener, array('//', '/*'))) {
                        for ($j = $i + 1; $j < strlen($content); $j++) {
                            $character = $content[$j];
                            $previousCharacter = isset($content[$j - 1]) ? $content[$j - 1] : '';
                            if (
                                ($commentOpener == '//' && in_array($character, array("\r", "\n")))
                                ||
                                ($commentOpener == '/*' && $previousCharacter . $character == '*/')
                            ) {
                                $replacement = '[MINIFY-COMMENT-' . count($comments) . ']';
                                $comments[$replacement] = substr($content, $i, $j - $i + 1);
                                $content = substr_replace($content, $replacement, $i, $j - $i + 1);
                                $i += strlen($replacement);
                                break;
                            }
                        }
                    }
                    break;
            }
        }
        return [$content, $strings, $comments];
    }

    /**
     * Minify the data.
     * Perform JS optimizations.
     *
     * @param string[optional] $path The path the data should be written to.
     * @param int[optional] $options The minify options to be applied.
     * @return string The minified data.
     */
    public function minify($content)
    {
        list ($content, $strings, $comments) = $this->extract($content);
        $content = $this->stripComments($content, $comments);
        $content = $this->stripWhitespace($content, $strings, $comments);
        $content = str_replace(array_keys($strings), array_values($strings), $content);
        return $content . ';';
    }

    /**
     * Strip comments from source code.
     *
     * @param string $content The file/content to strip the comments for.
     * @param string[optional] $path The path the data should be written to.
     * @return string
     */
    protected function stripComments($content, $comments)
    {
        return str_replace(array_keys($comments), array_fill(0, count($comments), ''), $content);
    }

    /**
     * Strip whitespaces in JS
     *
     * @param string $content
     * @return string
     */
    protected function stripWhitespace($content)
    {
        $content = str_replace(array("\r\n", "\r", "\n"), "\n", $content);
        $content = preg_replace('/^[ \t]*|[ \t]*$/m', '', $content);
        $content = preg_replace('/\n+/m', "\n", $content);
        $content = trim($content);
        $content = preg_replace(
            '/(?<=[{}\[\]\(\)=><&\|;:,\?!\+-])[ \t]*|[ \t]*(?=[{}\[\]\(\)=><&\|;:,\?!\+-])/i',
            '',
            $content
        );
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $content = preg_replace('/;\s*(?=[;}])/s', ' ', $content);
        return $content;
    }
}