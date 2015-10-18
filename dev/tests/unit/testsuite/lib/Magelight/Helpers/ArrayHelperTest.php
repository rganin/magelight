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

namespace Magelight\Helpers;

/**
 * Class ArrayHelperTest
 * @package Magelight\Helpers
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{

    public function dataProvider()
    {
        return [
            // array           insert     after                result
            [[1, 2, 3, 4, 5],        6,       3,  [1, 2, 3, 6, 4, 5]],
            [[1, 2, 3, 4, 5],        0,    5,     [1, 2, 3, 4, 5, 0]],
        ];
    }

    /**
     * ArrayHelper test
     *
     * @param array $sourceArray
     * @param mixed $insert
     * @param mixed $after
     * @param array $result
     *
     * @test
     *
     * @dataProvider dataProvider
     */
    public function arrayHelperTest($sourceArray, $insert, $after, $result)
    {
        $helper = new \Magelight\Helpers\ArrayHelper;
        /* @var $helper \Magelight\Helpers\ArrayHelper */
        $this->assertEmpty(array_diff($helper->insertToArray($sourceArray, $after, $insert), $result));
    }
}
