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

namespace Magelight\I18n;
/**
 * Class Translator
 * @package Magelight\I18n
 *
 * @method static \Magelight\I18n\Translator getInstance()
 */
class Translator
{
    const LANG_DEFAULT = 'en';

    use \Magelight\Traits\TForgery;

    /**
     * Translated strings array
     *
     * @var array
     */
    protected $_translations = [];

    /**
     * Plural function and count array
     *
     * @var array
     */
    protected $_plurals;

    /**
     * Load translations
     *
     * @param string $lang
     */
    public function loadTranslations($lang)
    {
        if (!is_string($lang)) {
            throw new \InvalidArgumentException('Language must be a string');
        }
        foreach (array_reverse(\Magelight::app()->getModuleDirectories()) as $modulesDir) {
            foreach (\Magelight\Components\Modules::getInstance()->getActiveModules() as $module) {
                $filename = $modulesDir . DS . $module['path'] . DS . 'I18n' . DS . $lang . '.php';
                if (file_exists($filename)) {
                    $translations = require $filename;
                    $this->_translations = array_replace_recursive($this->_translations, $translations);
                }
            }
        }
        $filename = __DIR__ . DS . 'Preferences' . DS . $lang . '.php';
        if (file_exists($filename)) {
            $plurals = require_once $filename;
            $this->_plurals = $plurals;
        }
    }

    /**
     * Translate string
     *
     * @param string $string
     * @param array $arguments
     * @param int $number
     * @param string $context
     * @return string
     */
    public function translate($string, $arguments, $number, $context)
    {

        if (!isset($this->_translations[$string][$context])) {
            $context = 'default';
        }
        if (!isset($this->_translations[$string][$context][$this->_plurals['plural_function']($number)])) {
            if (!empty($arguments) && !is_array($arguments)) {
                $arguments = [$arguments];
            }
            if (!isset($this->_translations[$string][$context][1])) {
                return vsprintf($string, $arguments);
            } else {
                return vsprintf($this->_translations[$string][$context][1], $arguments);
            }
        } else {
            if (!empty($arguments) && !is_array($arguments)) {
                $arguments = [$arguments];
            }
            return vsprintf(
                $this->_translations[$string][$context][$this->_plurals['plural_function']($number)], $arguments
            );
        }
    }

    /**
     * Get existing languages preferences
     *
     * @return array
     */
    public function getExistingPreferences()
    {
        $preferences = [];
        $dir = glob(__DIR__ . DS . 'Preferences' . DS . '*.php');
        foreach ($dir as $filename) {
            preg_match('~([\w\d-_]+)\.[\w\d]{1,4}~i', $filename, $matches);
            if (isset($matches[1])) {
                $preferences[$matches[1]] = include $filename;
            }
        }
        return $preferences;
    }
}
