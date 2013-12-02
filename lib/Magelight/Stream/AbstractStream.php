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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
