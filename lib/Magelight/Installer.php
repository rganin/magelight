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

    use Traits\TForgery;

    /**
     * Start setup
     *
     * @param string $index
     * @return $this
     */
    public function startSetup($index = \Magelight\App::DEFAULT_INDEX)
    {
        return $this;
    }

    /**
     * End setup
     *
     * @param string $index
     * @return $this
     */
    public function endSetup($index = \Magelight\App::DEFAULT_INDEX)
    {
        return $this;
    }

    /**
     *
     *
     * @param string $index
     * @return Db\Common\Adapter
     */
    public function getDb($index = \Magelight\App::DEFAULT_INDEX)
    {
        return \Magelight\App::getInstance()->db($index);
    }

    /**
     * Execute setup script
     *
     * @param $file
     * @throws Exception
     */
    public function executeScript($file)
    {
        if (!is_readable($file)) {
            throw new Exception("Install or upgrade script `{$file}` not found.");
        }
        $this->startSetup();
        include $file;
        $this->endSetup();
    }

    /**
     * Find install scripts
     *
     * @param $modulePath
     * @return array
     */
    public function findInstallScripts($modulePath)
    {
        $modulePath = str_replace('\\', DS, $modulePath);
        $scripts = [];
        $modulesDirs = array_reverse(\Magelight\App::getInstance()->getModuleDirectories());
        foreach ($modulesDirs as $modulesDir) {
            $path = $modulesDir . DS . $modulePath . DS . 'setup';
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

    /**
     * Check was setup script executed before
     *
     * @param string $moduleName
     * @param string $scriptName
     * @return bool
     */
    public function isSetupScriptExecuted($moduleName, $scriptName)
    {
        $file = \Magelight\App::getInstance()->getAppDir()
            . DS
            . \Magelight\Config::getInstance()->getConfig(
                'global/setup/executed_scripts/filename',
                'var/executed_setup.json'
            );

        if (!file_exists($file)) {
            if (!file_exists(dirname($file))) {
                mkdir(dirname($file), 0755, true);
            }
            file_put_contents($file, '');
        }
        $scripts = json_decode(file_get_contents($file), true);
        return isset($scripts[$moduleName][basename($scriptName)]);
    }

    /**
     * Set script as executed
     *
     * @param string $moduleName
     * @param string $scriptFullPath
     * @return App
     */
    public function setSetupScriptExecuted($moduleName, $scriptFullPath)
    {
        $file = \Magelight\App::getInstance()->getAppDir()
            . DS
            . \Magelight\Config::getInstance()->getConfig(
                'global/setup/executed_scripts/filename',
                'var/executed_setup.json'
            );

        if (file_exists($file)) {
            $scripts = json_decode(file_get_contents($file), true);
        } else {
            mkdir(dirname($file), 0755, true);
        }
        $scripts[$moduleName][basename($scriptFullPath)] = [date('Y-m-d H:i:s', time()), $scriptFullPath];
        $scripts = json_encode($scripts, JSON_PRETTY_PRINT);
        file_put_contents($file, $scripts);
        return $this;
    }
}