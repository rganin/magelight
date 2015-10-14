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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


namespace Magelight;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magelight\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appMock;

    /**
     * @var \Magelight\Components\Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesMock;

    /**
     * @var \Magelight\Db\Mysql\Adapter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbMock;

    /**
     * @var \Magelight\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magelight\Installer
     */
    protected $installer;

    public function setUp()
    {
        $this->modulesMock = $this->getMock(\Magelight\Components\Modules::class, [], [], '', false);
        $this->configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
        $this->dbMock= $this->getMock(\Magelight\Db\Mysql\Adapter::class, [], [], '', false);
        $this->appMock = $this->getMockForAbstractClass(App::class, [], '', false, false, true, ['db']);
        \Magelight\Components\Modules::forgeMock($this->modulesMock);
        \Magelight\Config::forgeMock($this->configMock);
        \Magelight\Db\Mysql\Adapter::forgeMock($this->dbMock);
        \Magelight\App::forgeMock($this->appMock);
    }

    public function testCreate()
    {
        $this->appMock->expects($this->any())->method('db')->will($this->returnValue($this->dbMock));
        $this->dbMock->expects($this->once())->method('execute')->with(
            $this->matchesRegularExpression('/CREATE TABLE.*/')
        );
        \Magelight\Installer::forge();
    }

    /**
     * @param $rowsCount
     * @param $expctedResult
     * @dataProvider isSetupScriptExecutedDataProvider
     */
    public function testIsSetupScriptExecuted($rowsCount, $expctedResult)
    {
        $this->appMock->expects($this->any())->method('db')->will($this->returnValue($this->dbMock));
        $this->dbMock->expects($this->at(0))->method('execute')->with(
            $this->matchesRegularExpression('/CREATE TABLE.*/')
        );
        $installer = \Magelight\Installer::forge();

        $pdoStatementMock = $this->getMock(\PDOStatement::class, [], [], '', false);
        $this->dbMock->expects($this->at(0))->method('execute')->with(
            $this->matches(
                "SELECT COUNT(*) FROM "
                . $installer->getVersionTable()
                . " WHERE module_name=? AND setup_script=?"
            ),
            ['Module1', 'setup-1.0.0.0.php']
        )->will($this->returnValue($pdoStatementMock));
        $pdoStatementMock->expects($this->once())->method('fetchColumn')->will($this->returnValue($rowsCount));
        $this->assertEquals($expctedResult, $installer->isSetupScriptExecuted('Module1', 'setup-1.0.0.0.php'));
    }

    public function isSetupScriptExecutedDataProvider()
    {
        return[
            [0, false],
            [1, true]
        ];
    }

    public function setSetupScriptExecutedTest()
    {
        $this->appMock->expects($this->any())->method('db')->will($this->returnValue($this->dbMock));
        $this->dbMock->expects($this->at(0))->method('execute')->with(
            $this->matchesRegularExpression('/CREATE TABLE.*/')
        );
        $installer = \Magelight\Installer::forge();
        $this->dbMock->expects($this->at(0))->method('execute')->with(
            $this->matches(
                "INSERT INTO `"
                . $installer->getVersionTable()
                . "` (module_name, setup_script) VALUES (?, ?)"
            ),
            ['Module1', 'setup-1.0.0.0.php']
        );
        $this->assertInstanceOf(
            \Magelight\Installer::class,
            $installer->setSetupScriptExecuted('Module1', 'setup-1.0.0.0.php')
        );
    }
}
