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

namespace Magelight\Components\Loaders;

/**
 * Class ConfigTest
 * @package Magelight\Components\Loaders
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $_xml1 =
    '<config>
        <test>
            <file>logfile.log</file>
        </test>
    </config>';

    /**
     * @var string
     */
    protected $_xml2 =
    '<config>
        <test>
            <file>override</file>
        </test>
    </config>';

    /**
     * @test
     */
    public function configMergeTest()
    {
        $xml1 = new \SimpleXMLElement($this->_xml1);
        $xml2 = new \SimpleXMLElement($this->_xml2);

        \Magelight\Components\Loaders\Config::mergeConfig($xml1, $xml2);

        $this->assertEquals('override', (string)$xml1->test->file);
    }
}
