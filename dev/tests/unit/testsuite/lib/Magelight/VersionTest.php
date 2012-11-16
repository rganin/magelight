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
namespace Magelight;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    const RESULT_EQUAL   =  0;
    const RESULT_GREATER =  1;
    const RESULT_LOWER   = -1;

    /**
     * Result =
     * 0 - equal,
     * 1 - gt,
     * -1 - lt
     *
     * @return array
     */
    public function versionTestDataProvider()
    {
        return [
            ['1.1.1.1',     '1.1.1.1',     self::RESULT_EQUAL],
            ['1.1.1.1',     '1.1.2.1',     self::RESULT_LOWER],
            ['1.1.1.1',     '1.0.0.7',     self::RESULT_GREATER],
            ['1.1.1.1',     '1.1.1.1-RC9', self::RESULT_EQUAL],
            ['1.2.3.4-RC1', '1.2.3.4-RC9', self::RESULT_LOWER]
        ];
    }

    /**
     * Test version class
     *
     * @test
     *
     * @param $versionLeft
     * @param $versionRight
     * @param $result
     *
     * @dataProvider versionTestDataProvider
     */
    public function versionTest($versionLeft, $versionRight, $result)
    {
        $version = new \Magelight\Version($versionLeft);
        if ($result === 0) {
            $this->assertTrue($version->isEqual($versionRight));
        } elseif ($result < 0) {
            $this->assertTrue($version->isLowerThan($versionRight));
        } else {
            $this->assertTrue($version->isGreaterThan($versionRight));
        }
    }
}
