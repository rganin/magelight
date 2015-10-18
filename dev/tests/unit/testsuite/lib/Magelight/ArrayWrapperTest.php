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
 * Class ArrayWrapperTest
 * @package Magelight
 */
class ArrayWrapperTest extends \Magelight\TestCase
{
    public function testArrayWrapper()
    {
        $array = [
            'node_1' => 'node1value',
            'node_2' => 'node2value',
            'node_3' => 'node3value',
            'node_4' => 'node4value',
            'node_5' => 'node5value',
        ];
        $arrayWrapper = \Magelight\ArrayWrapper::forge($array);

        $this->assertEquals('node1value', $arrayWrapper->getData('node_1'));
        $this->assertEquals(null, $arrayWrapper->getData('node_unexistent'));

        $arrayWrapper->setData('node_6', 'node6value');
        $this->assertEquals('node6value', $arrayWrapper->getData('node_6'));
        $this->assertEquals('node6value', $arrayWrapper->node_6);

        $this->assertTrue($arrayWrapper->allElementsExist(['node_1', 'node_2', 'node_3']));
        unset($arrayWrapper->node_3);
        $this->assertFalse($arrayWrapper->allElementsExist(['node_1', 'node_2', 'node_3']));
        $this->assertTrue(isset($arrayWrapper->node_1));
        $arrayWrapper->other_data = 'other';
        $this->assertEquals('other', $arrayWrapper->other_data);
        $this->assertEquals('other', $arrayWrapper->getData('other_data'));

        $this->assertFalse($arrayWrapper->allElementsExist('node_1', 'node_2', 'node_3'));
        $this->assertFalse($arrayWrapper->allElementsExist());
    }
}
