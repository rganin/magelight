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
    use \Magelight\Forgery;

    protected $_fileData = [];

    public function __forge($data = [])
    {
        $this->setFileData($data);
    }

    public function setFileData($data = [])
    {
        $this->_fileData = $data;
        return $this;
    }

    public function getName()
    {
        return isset($this->_fileData['name']) ? $this->_fileData['name'] : null;
    }

    public function getTmpName()
    {
        return isset($this->_fileData['tmp_name']) ? $this->_fileData['tmp_name'] : null;
    }

    public function getSize()
    {
        return isset($this->_fileData['size']) ? $this->_fileData['size'] : null;
    }

    public function getError()
    {
        return isset($this->_fileData['error']) ? $this->_fileData['error'] : null;
    }

    public function getType()
    {
        return isset($this->_fileData['type']) ? $this->_fileData['type'] : null;
    }

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
