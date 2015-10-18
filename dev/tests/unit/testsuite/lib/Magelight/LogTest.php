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
 * Class LogTest
 * @package Magelight
 */
class LogTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Log|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $log;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->log = $this->getMockForAbstractClass(\Magelight\Log::class, [], '', false, false, true, ['writeMessage']);
    }

    public function testAdd()
    {
        $configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
        \Magelight\Config::forgeMock($configMock);
        $configMock->expects($this->any())
            ->method('getConfig')
            ->with('global/log/file', \Magelight\Log::DEFAUL_LOG_FILE)
            ->will($this->returnValue('application.log'));

        $this->log->expects($this->once())->method('writeMessage')->with(
            $this->matchesRegularExpression("/[\-\:\d]+\s\-\serror/i")
        );

        $this->log->add('error');
    }
}
