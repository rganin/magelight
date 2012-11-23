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

namespace Magelight\Webform\Models\Validation;

class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkerGeneralTest()
    {
        $checker = \Magelight\Webform\Models\Validator::forge()
            ->fieldRules('login', 'Login field')->max(33)
            ->chainRule()->required()
            ->checker();
        $this->assertTrue($checker->check(30));
        $this->assertFalse($checker->check(null));
        $this->assertFalse($checker->check(100500));
    }
}