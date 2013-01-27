<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 07.01.13
 * Time: 2:31
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

/**
 * @method static \Magelight\Upload forge($data = [])
 */
class Upload
{
    /**
     * Using forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Uploaded file data
     *
     * @var array
     */
    protected $_fileData = [];

    /**
     * Forgery constructor
     *
     * @param array $data
     */
    public function __forge($data = [])
    {
        $this->setFileData($data);
    }

    /**
     * Set uploaded file data
     *
     * @param array $data
     * @return Upload
     */
    public function setFileData($data = [])
    {
        $this->_fileData = $data;
        return $this;
    }

    /**
     * Get upload name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->_fileData['name']) ? $this->_fileData['name'] : null;
    }

    /**
     * Get upload TMP name
     *
     * @return string|null
     */
    public function getTmpName()
    {
        return isset($this->_fileData['tmp_name']) ? $this->_fileData['tmp_name'] : null;
    }

    /**
     * Get upload size
     *
     * @return int|null
     */
    public function getSize()
    {
        return isset($this->_fileData['size']) ? $this->_fileData['size'] : null;
    }

    /**
     * Get upload error code
     *
     * @return int|null
     */
    public function getError()
    {
        return isset($this->_fileData['error']) ? $this->_fileData['error'] : null;
    }

    /**
     * Get upload mime type
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->_fileData['type']) ? $this->_fileData['type'] : null;
    }

    /**
     * Save upload to file
     *
     * @param string $path
     * @param string|null $filename
     * @param bool $createPath
     * @param int $mode
     * @return bool
     */
    public function saveTo($path, $filename = null, $createPath = true, $mode = 755)
    {
        if (empty($filename)) {
            $filename = $this->getName();
        }
        $path = str_ireplace('\\/', DS, rtrim($path, '\\/') . DS . $filename);
        $dir = dirname($path);
        if ($createPath && !file_exists($dir)) {
            mkdir($dir, $mode, true);
        }
        if (!is_writable($dir)) {
            trigger_error("Moving upload failed. Path {$dir} is not writable.", E_USER_WARNING);
        }
        return move_uploaded_file($this->getTmpName(), $path);
    }
}
