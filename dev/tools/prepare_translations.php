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

class Tokenizer
{
    protected $_foundStrings = [];

    /**
     * Find where translate function is called
     *
     * @param array $tokens
     * @param int $start
     * @param string $filename
     * @param array $foundStrings
     */
    protected function _findTranslateFunctionCalls($tokens, $start, $filename, &$foundStrings = [])
    {
        for ($key = $start; $key <= (count($tokens)); $key++) {
            if (!isset($tokens[$key])) {
                continue;
            }
            $token = $tokens[$key];
            if (is_array($token) && $token[0] == 307 && $token[1] == '__') {
                $call = [];
                $fullCall = [];
                $marker = 0;
                $i = $key + 1;
                do {
                    if (isset($tokens[$i]) && is_string($tokens[$i]) && $tokens[$i] == '(') {
                        $marker++;
                    } elseif ((isset($tokens[$i]) && is_string($tokens[$i]) && $tokens[$i] == ')')) {
                        $marker--;
                    } elseif ($marker > 0 && is_array($tokens[$i]) && $tokens[$i][0] == 307 && $tokens[$i][1] == '__') {
                        $this->_findTranslateFunctionCalls($tokens, $i, $filename, $foundStrings);
                    } elseif ($marker > 0){
                        $call[] = $tokens[$i];
                    }
                    $fullCall[] = $tokens[$i];

                    $i++;
                } while ($marker != 0 || $i >= count($tokens));
                $callArgs = [];
                foreach ($call as $callKey => $callToken) {
                    $callArgs[] = is_string($callToken) ? $callToken : $callToken[1];
                }
                $calledString = [];
                foreach ($callArgs as $arg) {
                    if ($arg !== ',') {
                        $calledString[] = $arg;
                    } else {
                        break;
                    }
                }
                $string = '';
                if(!empty($callArgs[0])) {
                    eval ('$this->_parseConcatenationStub(' . implode($calledString) . ', $string);');
                }
                $foundStrings[$string]['called_in'][$filename . ':' . $token[2]] = 1;

            }
        }
    }

    /**
     * Get translation calls in file
     *
     * @param string $filename
     * @return array
     */
    public function findTranslations($filename)
    {
        $this->_foundStrings = [];
        $tokens = token_get_all(file_get_contents($filename));
        $this->_findTranslateFunctionCalls($tokens, 0, $filename, $this->_foundStrings);
//        var_dump($this->_foundStrings);
        return $this->_foundStrings;
    }

    /**
     * Concatenation stub
     *
     * @param string $string
     * @param string $destination
     */
    protected function _parseConcatenationStub($string, &$destination)
    {
        $destination = $string;
    }
}





class Crawler
{
    /**
     * Translations array
     *
     * [
     *    'Magelight\Module' => [phrases]
     *    'Custom\AppModule' => [phrases]
     *      ...
     * ]
     *
     * @var array
     */
    protected $_phrases = [];

    /**
     * Array of file types to scan
     *
     * @var array
     */
    protected $_fileTypes = ['*.php', '*.phtml'];

    /**
     * @var \Tokenizer
     */
    protected $_tokenizer;

    /**
     * @var array
     */
    protected $_langPreferences;

    /**
     * Crawl application for translations
     */
    public function crawlApp()
    {
        $files = [];
        foreach (array_reverse(\Magelight::app()->getModuleDirectories()) as $modulesDir) {
            foreach (\Magelight\Components\Modules::getInstance()->getActiveModules() as $module) {
                $path = $modulesDir . DS . $module['path'];
                if (is_readable($path)) {
                    foreach ($this->_getModuleFilesList($path) as $foundFile) {
                        $files[$path][$foundFile] = [];
                    }
                }
            }
        }

        foreach ($files as $modulePath => $filesList) {
            foreach ($filesList as $filePath => $translations) {
                echo 'fetching translations for ' . $filePath . PHP_EOL;
                $translations = $this->_getTokenizer()->findTranslations($filePath);
                if (!empty($translations)) {
                    if (!isset($this->_phrases[$modulePath])) {
                        $this->_phrases[$modulePath] = [];
                    }
                    $this->_phrases[$modulePath] = array_merge_recursive($this->_phrases[$modulePath], $translations);
                }
            }
        }

        $this->_processLanguagePreferences();
    }

    protected function _processLanguagePreferences()
    {
        foreach ($this->_loadLanguagePreferences() as $lang => $preferences) {
            foreach ($this->_phrases as $modulePath => $translations) {
                $this->_processModulesTranslationsForLanguage($modulePath, $lang, $preferences);
            }
        }
    }

    protected function _hasExistingTranslations($translationsArray, $phrase, $languagePreferences)
    {

    }

    protected function _processModulesTranslationsForLanguage($modulePath, $lang, $languagePreferences)
    {
        $moduleTranslations = [];
        $existingTranslations = [];
        $filename = $modulePath . DS . 'I18n' . DS . $lang . '.php';
        if (file_exists($filename)) {
            $existingTranslations = include $filename;
        }
        $moduleTranslations = array_merge_recursive($moduleTranslations, $existingTranslations);
        foreach ($this->_phrases[$modulePath] as $phrase => $calls) {
            if (!isset($moduleTranslations[$phrase])) {
                $moduleTranslations[$phrase] = [
                    'default' => array_fill(1, $languagePreferences['plural_forms'], null)
                ];
            }
        }
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($filename, $this->_prepareTranslationContent($moduleTranslations));

        //load module existing language translation
        //if not exists - create new file
        //pass thru loaded phrases and set non-empty translations from existing ones
        //set translation draft for non-existing ones
        //overwrite existing translation with new one

    }

    protected function _prepareTranslationContent(array $translationContent)
    {
        $content = "<?php
/**
 * [
 *     'phrase' => [
 *          'context' => [
 *              //plural forms
 *              1 => single
 *              2 => double
 *              3 => triple or many
 *          ]
 *      ]
 *      .....
 * ]
 */" . PHP_EOL;
        $content .= 'return ' . $this->_varExport54($translationContent);
        $content .= ';' . PHP_EOL;
        return $content;
    }

    function _varExport54($var, $indent="") {
        switch (gettype($var)) {
            case "string":
                $charlist = "\\\'\r\n\t\v\f";
                $encapser = '\'';
                if (strpos($var, "\n") !== false) {
                    $charlist = "\\\"\t\v\f";
                    $encapser = '"';
                }
                return $encapser . addcslashes($var, $charlist) . $encapser;
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->_varExport54($key) . " => ")
                        . $this->_varExport54($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "true" : "false";
            default:
                return var_export($var, true);
        }
    }

    protected function _loadLanguagePreferences()
    {
        return \Magelight\I18n\Translator::getInstance()->getExistingPreferences();
    }

    protected function _getTokenizer()
    {
        if (!$this->_tokenizer instanceof Tokenizer) {
            $this->_tokenizer = new Tokenizer();
        }
        return $this->_tokenizer;
    }

    /**
     * Get files to scan for module
     *
     * @param string $modulePath
     * @return array
     */
    protected function _getModuleFilesList($modulePath)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($modulePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        $paths = array();
        foreach ($iterator as $subPath => $filePath) {
            if ($filePath->isFile() && $this->_isNameMatchedByFileTypes($subPath)) {
                $paths[] = $subPath;
            }
        }
        return $paths;
    }

    /**
     * Is name matched by file types
     *
     * @param string $filename
     * @return bool
     */
    protected function _isNameMatchedByFileTypes($filename)
    {
        foreach ($this->_fileTypes as $type) {
            if (fnmatch($type, $filename)) {
                return true;
            }
        }
        return false;
    }
}
$options = getopt("p:l:");

if (!isset($options['p'])) {
    echo 'Usage:' . PHP_EOL;
    echo 'php -f prepare_translations.php -- -p path/to/application';
    die();
}

require_once realpath($options['p']) . '/bootstrap.php';
Magelight::app()->setAppDir(realpath($options['p']))->init();

$crawler = new Crawler();
$crawler->crawlApp();
