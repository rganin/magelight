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

namespace Magelight;

/**
 * Installer class
 *
 * @method static \Magelight\Installer forge()
 */
class Installer
{
    use Traits\TForgery;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->createVersionTableIfNotExists();
    }

    /**
     * Start setup
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function startSetup()
    {
        return $this;
    }

    /**
     * End setup
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function endSetup()
    {
        return $this;
    }

    /**
     * Get DB instance
     *
     * @return Db\Common\Adapter
     */
    public function getDb()
    {
        return \Magelight\App::getInstance()->db();
    }

    /**
     * Execute setup script
     *
     * @param $file
     * @throws Exception
     * @codeCoverageIgnore
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
     * Get version table
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getVersionTable()
    {
        return 'magelight_version';
    }

    /**
     * Create version table
     *
     * @throws Exception
     */
    protected function createVersionTableIfNotExists()
    {
        $this->getDb()->execute(
            "CREATE TABLE IF NOT EXISTS`{$this->getVersionTable()}` (
                `module_name` VARCHAR(64) NOT NULL,
                `setup_script` VARCHAR(32) NOT NULL,
                INDEX `module_name` (`module_name`),
                INDEX `setup_script` (`setup_script`),
                UNIQUE INDEX `module_name_setup_script` (`module_name`, `setup_script`)
            )ENGINE=InnoDB;"
        );
    }

    /**
     * Upgrade modules
     *
     * @return Installer
     * @codeCoverageIgnore
     */
    public function upgrade()
    {
        foreach (\Magelight\Components\Modules::getInstance()->getActiveModules() as $module) {
            $scripts = $this->findInstallScripts($module['path']);
            foreach ($scripts as $script => $filename) {
                if (!$this->isSetupScriptExecuted($module['name'], $script)) {
                    $this->executeScript($filename);
                    $this->setSetupScriptExecuted($module['name'], $script);
                }
            }
        }
        return $this;
    }

    /**
     * Find install scripts
     *
     * @param $modulePath
     * @return array
     * @codeCoverageIgnore
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
                     * Finding install scripts in modules sequence and not allowing to override them
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
        $result = (int)$this->getDb()->execute(
            "SELECT COUNT(*) FROM {$this->getVersionTable()} WHERE module_name=? AND setup_script=?",
            [$moduleName, $scriptName]
        )->fetchColumn();

        return $result > 0;
    }

    /**
     * Set script as executed
     *
     * @param string $moduleName
     * @param string $scriptName
     * @return $this
     */
    public function setSetupScriptExecuted($moduleName, $scriptName)
    {
        $this->getDb()->execute(
            "INSERT INTO `{$this->getVersionTable()}` (module_name, setup_script) VALUES (?, ?)",
            [
                $moduleName,
                $scriptName
            ]
        );
        return $this;
    }
}
