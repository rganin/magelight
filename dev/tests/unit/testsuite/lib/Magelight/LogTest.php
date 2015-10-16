<?php

namespace Magelight;

class LogTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Log|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $log;

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
