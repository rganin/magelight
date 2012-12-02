<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 20:01
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function findScriptsTest()
    {
        $installer = Installer::forge();
        var_dump($installer->findInstallScripts('Board'));
    }
}