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
 * Class UploadTest
 * @package Magelight
 */
class UploadTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Upload
     */
    protected $upload;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $data = [
            'name' => 'file.txt',
            'tmp_name' => 'file.txt.tmp',
            'size' => 123,
            'error' => null,
            'type' => 'application/octet-stream'
        ];

        $this->upload = Upload::forge($data);
    }

    public function testGetName()
    {
        $this->assertEquals('file.txt', $this->upload->getName());
    }

    public function testGetTmpName()
    {
        $this->assertEquals('file.txt.tmp', $this->upload->getTmpName());
    }

    public function testGetSize()
    {
        $this->assertEquals(123, $this->upload->getSize());
    }

    public function testGetError()
    {
        $this->assertNull($this->upload->getError());
    }

    public function testGetType()
    {
        $this->assertEquals('application/octet-stream', $this->upload->getType());
    }

    public function testHasAllowedExtension()
    {
        $this->upload->setAllowedExtensions(['txt', 'jpg', 'gif']);
        $this->assertTrue($this->upload->hasAllowedExtension(['txt', 'jpg', 'gif']));
        $this->assertFalse($this->upload->hasAllowedExtension(['jpg', 'gif']));
        $this->assertTrue($this->upload->hasAllowedExtension());
    }

    public function testHasRestrictedExtension()
    {
        $this->upload->setRestrictedExtensions(['txt']);
        $this->assertTrue($this->upload->hasRestrictedExtension(['txt', 'jpg', 'gif']));
        $this->assertFalse($this->upload->hasRestrictedExtension(['jpg', 'gif']));
        $this->assertTrue($this->upload->hasRestrictedExtension());
    }
}
