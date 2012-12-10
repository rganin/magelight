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

final class Magelight
{
    /**
     * Application
     * 
     * @var \Magelight\App
     */
    protected static $_app = null;

    /**
     * @static
     * Get application object
     * 
     * @return \Magelight\App|null
     */
    public static function app()
    {
        if (empty(self::$_app)) {
            self::$_app = new \Magelight\App();
        }
        return self::$_app;
    }

    /**
     * Get session object
     * 
     * @static
     * @return Magelight\Http\Session
     */
    public static function session()
    {
        return \Magelight\Http\Session::getInstance();
    }

    /**
     * Get helper by name
     * @static
     * 
     * @param $name
     *
     * @return mixed
     * @throws Magelight\Exception
     */
    public static function helper($name)
    {
        $name = ucfirst($name);
        try {
            $helper = call_user_func(array('\\Magelight\\Helpers\\' . $name . 'Helper', 'getInstance'));
        } catch (Exception $e) {
            throw new \Magelight\Exception($e->getMessage());
        }
        return $helper;
    }

    /**
     * Dump variable to STD or SOCKET output
     * 
     * @static
     *
     * @param mixed $var
     */
    public static function dump($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
    
    /**
     * Just autoloader
     * 
     * @static
     * @param string $className
     */
    public static function autoload($className)
    {
        require_once self::getAutoloaderFileNameByClass($className);
    }

    /**
     * Get autoloader fielname by class
     *
     * @param $className
     * @return string
     */
    public static function getAutoloaderFileNameByClass($className)
    {
        return $className . '.php';
    }

    /**
     * Translate string
     * todo:Implement translator :)
     *
     * @param string $traslate
     * @param int $num
     * @param string $context
     * @param array $arguments
     *
     * @return string
     */
    public static function __($traslate, $num = 1, $context = 'default', $arguments = [])
    {
        return vsprintf($traslate, $arguments);
    }
}
