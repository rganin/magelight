<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 18:47
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

/**
 * @method static \Magelight\Installer forge()
 */
class Installer
{
    const DEFAULT_DB_INDEX = 'default';

    use Forgery;

    public function startSetup()
    {

    }

    public function endSetup()
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