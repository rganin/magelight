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
