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

class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function genericBlockTest()
    {
        $block = \UnitTests\TestBlock::forge();
        /* @var $block \UnitTests\TestBlock */
        $this->assertTrue($block->init() instanceof \UnitTests\TestBlock);
        $this->assertTrue(\UnitTests\TestBlock::forge() instanceof \UnitTests\TestBlock);
        $this->assertTrue(\UnitTests\TestBlock::getInstance() instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->setTemplate('') instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->set('', null) instanceof \UnitTests\TestBlock);

        $this->assertTrue(is_string(@$block->section('123')));
        // block genenrates a E_USER_NOTICE on undefined section call so muting all notices
        $this->assertTrue($block->sectionAppend('123', $block) instanceof \UnitTests\TestBlock);

        $this->assertTrue($block->sectionPrepend('123', $block) instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->sectionReplace('123', $block) instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->sectionDelete('123') instanceof \UnitTests\TestBlock);
    }

    /**
     * @test
     */
    public function genericBlockForgeryTest()
    {
        $block = \UnitTests\TestBlock::forge();
        /* @var $block \UnitTests\TestBlock */
        $this->assertTrue(($block->getCurrentModuleName() === 'UnitTests'));
    }
}