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

namespace Magelight;

/**
 * Installer class
 *
 * @method static \Magelight\Installer forge()
 */
class Installer
{
    const DEFAULT_DB_INDEX = 'default';

    use TForgery;

    public function startSetup($index = \Magelight\App::DEFAULT_INDEX)
    {

    }

    public function endSetup($index = \Magelight\App::DEFAULT_INDEX)
    {

    }

    /**
     *
     *
     * @param string $index
     * @return Db\Common\Adapter
     */
    public function getDb($index = \Magelight\App::DEFAULT_INDEX)
    {
        return \Magelight::app()->db($index);
    }

    public function executeScript($file)
    {
        if (!is_readable($file)) {
            throw new Exception("Install or upgrade script `{$file}` not found.");
        }
        $this->startSetup();
        include $file;
        $this->endSetup();
    }

    public function findInstallScripts($modulePath)
    {
        $modulePath = str_replace('\\', DS, $modulePath);
        $scripts = [];
        $pools = \Magelight::app()->getCodePools();
        foreach ($pools as $pool) {
            $path = \Magelight::app()->getAppDir() . DS . 'modules' . DS . $pool . DS . $modulePath . DS . 'setup';
            if (is_readable($path)) {
                foreach (glob($path . DS . '*[setup|install|upgrade]*.php') as $file) {
                    $basename = basename($file);
                    /**
                     * Finding install scripts in code pools with code pools sequence and not allowing to override them
                     */
                    if (!isset($scripts[$basename])) {
                        $scripts[$basename] = $file;
                    }
                }
            }
        }
        return $scripts;
    }
}