<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $this->assertTrue($block->initSections() instanceof \UnitTests\TestBlock);
        $this->assertTrue($block->set('', null) instanceof \UnitTests\TestBlock);
        $this->assertTrue(is_string($block->section('123')));
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