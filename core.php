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

define('PS', PATH_SEPARATOR);
define('DS', DIRECTORY_SEPARATOR);
define('FRAMEWORK_DIR', dirname(__FILE__));
$includePath = explode(PS, ini_get('include_path'));
array_unshift($includePath, realpath(FRAMEWORK_DIR . DS . 'lib'));
ini_set('include_path', implode(PS, $includePath));
require 'lib' . DS . 'Magelight' . DS . 'Autoload.php';
$autoloader = new \Magelight\Autoload();
spl_autoload_register([$autoloader, 'autoload']);

/**
 * Translation function
 *
 * @param string $string - translated string
 * @param array $arguments - arguments
 * @param int $number - plural number for plural forms
 * @param string $context - context
 * @return string
 */
function __($string, $arguments = [], $number = 1, $context = \Magelight\App::DEFAULT_INDEX)
{
    return \Magelight\I18n\Translator::getInstance()->translate($string, $arguments, $number, $context);
}
