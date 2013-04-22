<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 12.04.13
 * Time: 17:26
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Stream;

abstract class AbstractStream
{
    protected $_content = '';

    protected $_position = 0;

    protected $_stat = null;

    abstract public function stream_open($path, $mode, $options, &$openedPath);

    abstract public function url_stat($path , $flags);

    public function stream_read($count)
    {
        $block = substr($this->_content, $this->_position, $count);
        $this->_position += strlen($block);
        return $block;
    }

    public function stream_tell()
    {
        return $this->_position;
    }

    public function stream_eof()
    {
        return $this->_position >= strlen($this->_content);
    }

    public function stream_stat()
    {
        return $this->_stat;
    }

    public function stream_seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_CUR:
                $offset = $this->_position += $offset;
                break;
            case SEEK_END:
                $offset = $this->_position = strlen($this->_content) + $offset;
                break;
        }
        if ($offset < 0 || $offset >= strlen($this->_content)) {
            return false;
        }
        $this->_position = $offset;
        return true;
    }
}
