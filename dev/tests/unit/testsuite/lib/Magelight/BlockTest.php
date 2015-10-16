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

namespace UnitTests;

class TestBlock extends \Magelight\Block
{

}

namespace Magelight;

class BlockTest extends \Magelight\TestCase
{
    public function testGeneric()
    {
        $block = \UnitTests\TestBlock::forge();
        /* @var $block \UnitTests\TestBlock */
        $this->assertTrue($block->init() instanceof \UnitTests\TestBlock);
        $this->assertTrue(\UnitTests\TestBlock::forge() instanceof \UnitTests\TestBlock);
        $this->assertTrue(\UnitTests\TestBlock::getInstance() instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->setTemplate('') instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->set('', null) instanceof \UnitTests\TestBlock);

    }

    public function testSectionReplaceApendPrepend()
    {
        $blockMock1 = $this->getMock(\Magelight\Block::class, [], [], '', false);
        $blockMock1->expects($this->once())->method('init');
        $blockMock2 = $this->getMock(\Magelight\Block::class, [], [], '', false);
        $blockMock2->expects($this->once())->method('init');
        $blockMock1->expects($this->once())->method('toHtml')->will($this->returnValue('<div>Block1</div>'));
        $blockMock2->expects($this->once())->method('toHtml')->will($this->returnValue('<div>Block2</div>'));
        $block = \Magelight\Block::forge();
        $block->sectionReplace('body', $blockMock1);
        $block->sectionAppend('body', $blockMock2);
        $block->sectionAppend('body', '<div>AppendedString</div>');
        $block->sectionPrepend('body', '<div>PrependedString</div>');
        $expectedOutput = '<div>PrependedString</div><div>Block1</div><div>Block2</div><div>AppendedString</div>';
        $this->assertEquals($expectedOutput, $block->section('body'));
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Undefined section call - 'test' in Magelight\Block
     */
    public function testUnexistentSectionCall()
    {
        $block = \Magelight\Block::forge();
        $block->sectionReplace('body', 'BODY HTML');
        $block->section('test');
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Undefined section call - 'body' in Magelight\Block
     */
    public function testSectionDelete()
    {
        $block = \Magelight\Block::forge();
        $block->sectionReplace('body', 'BODY HTML');
        $block->sectionDelete('body');
        $block->section('body');
    }

    public function testToHtmlFromCache()
    {
        $cacheAdapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($cacheAdapterPoolMock);

        $cacheAdapterMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            []
        );

        $cacheAdapterPoolMock->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($cacheAdapterMock));

        $block = \Magelight\Block::forge();
        $block->useCache('cache_key');

        $cacheAdapterMock->expects($this->once())
            ->method('get')
            ->with('e4453472c2481215f49f028d80db65d7', null)
            ->will($this->returnValue('<div>cached html</div>'));

        $this->assertEquals('<div>cached html</div>', $block->toHtml());
    }

    public function testToHtmlCacheMiss()
    {
        $cacheAdapterPoolMock = $this->getMock(\Magelight\Cache\AdapterPool::class, [], [], '', false);
        \Magelight\Cache\AdapterPool::forgeMock($cacheAdapterPoolMock);

        $cacheAdapterMock = $this->getMockForAbstractClass(
            \Magelight\Cache\AdapterAbstract::class,
            [],
            '',
            false,
            false,
            true,
            []
        );

        $cacheAdapterPoolMock->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($cacheAdapterMock));

        $block = \Magelight\Block::forge();

        $block->useCache('cache_key');
        $block->setTemplate(__DIR__ . DS . '_fixtures' . DS . 'test_block_template.phtml');

        $cacheAdapterMock->expects($this->once())
            ->method('set')
            ->with('e4453472c2481215f49f028d80db65d7', '<div>7</div>', 3600);

        $this->assertEquals('<div>7</div>', $block->toHtml());
    }

    /**
     * @expectedException \Magelight\Exception
     * @expectedExceptionMessage Undeclared template in block 'Magelight\Block'
     */
    public function testToHtmlEmptyTemplate()
    {
        $block = \Magelight\Block::forge();
        $block->toHtml();
    }

    public function testInit()
    {
        $block = \Magelight\Block::forge();
        $this->assertTrue($block->init() instanceof \Magelight\Block);
    }

    public function testUrl()
    {
        $match = 'test';
        $params = ['xxx' => '1', 'yyy' => '2'];
        $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP;
        $addOnlyMaskParams = false;
        $urlHelperMock = $this->getMock(\Magelight\Helpers\UrlHelper::class, [], [], '', false);
        \Magelight\Helpers\UrlHelper::forgeMock($urlHelperMock);
        $urlHelperMock->expects($this->once())->method('getUrl')->with(
            $match, $params, $type, $addOnlyMaskParams
        )->will($this->returnValue('http://localhost/test?xxx=1&yyy=2'));
        $block = \Magelight\Block::forge();

        $this->assertEquals(
            'http://localhost/test?xxx=1&yyy=2',
            $block->url($match, $params, $type, $addOnlyMaskParams)
        );
    }

    public function testDate()
    {
        $configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
        $configMock->expects($this->once())
            ->method('getConfig')
            ->with('global/view/date_format', 'Y-m-d')
            ->will($this->returnValue('Y-m-d'));
        \Magelight\Config::forgeMock($configMock);
        $date = time();
        $expectedDateString = date('Y-m-d', $date);
        $this->assertEquals($expectedDateString, \Magelight\Block::forge()->date($date));
    }

    public function testDateTime()
    {
        $configMock = $this->getMock(\Magelight\Config::class, [], [], '', false);
        $configMock->expects($this->once())
            ->method('getConfig')
            ->with('global/view/date_time_format', 'Y-m-d H:i:s')
            ->will($this->returnValue('Y-m-d H:i:s'));
        \Magelight\Config::forgeMock($configMock);
        $date = time();
        $expectedDateString = date('Y-m-d H:i:s', $date);
        $this->assertEquals($expectedDateString, \Magelight\Block::forge()->dateTime($date));
    }

    public function testDateTimeCustom()
    {
        $date = time();
        $expectedDateString = date('Y-m-d H:i', $date);
        $this->assertEquals($expectedDateString, \Magelight\Block::forge()->dateTimeCustom('Y-m-d H:i', $date));
    }

    public function testEscapeHtml()
    {
        $html = '<div>html text</div>';
        $this->assertEquals('&lt;div&gt;html text&lt;/div&gt;', \Magelight\Block::forge()->escapeHtml($html));
    }

    public function testTruncate()
    {
        $text = 'Lorem ipsum dolor sit amet';
        $this->assertEquals('Lorem ipsum do...', \Magelight\Block::forge()->truncate($text, 14, '...'));
    }

    public function testTruncatePreserveWords()
    {
        $text = 'Lorem ipsum dolor sit amet';
        $this->assertEquals('Lorem ipsum dolor...', \Magelight\Block::forge()->truncatePreserveWords($text, 13, '...'));
    }
}
