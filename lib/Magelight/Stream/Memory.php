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

class Memory extends AbstractStream
{
    protected static $_virtualFiles = [];

    const NAME = 'memoryyy';

    public static function addVirtualContent($path, $content)
    {
        static::$_virtualFiles[$path] = $content;
    }

    public static function getVirtualContent($path, $default = '')
    {
        return isset(static::$_virtualFiles[$path]) ? static::$_virtualFiles[$path] : $default;
    }

    public static function register()
    {
        if (!in_array(self::NAME, stream_get_wrappers())) {
            stream_wrapper_register(self::NAME, get_called_class());
        }
    }

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        if (!isset(static::$_virtualFiles[$path])) {
            return true; // - mute error if virtual file does not exist, will appear on base class reached
        }
        $this->_content = static::$_virtualFiles[$path];
        return true;
    }

    public function url_stat($path, $flags)
    {
        return array (
            0 => 2,
            1 => 0,
            2 => 33206,
            3 => 1,
            4 => posix_getuid(),
            5 => posix_getgid(),
            6 => 2,
            7 => strlen(static::getVirtualContent($path)),
            8 => 1366038450,
            9 => 1366038450,
            10 => 1365759746,
            11 => -1,
            12 => -1,
            'dev' => 2,
            'ino' => 0,
            'mode' => 33206,
            'nlink' => 1,
            'uid' => posix_getuid(),
            'gid' => posix_getgid(),
            'rdev' => 2,
            'size' => strlen(static::getVirtualContent($path)),
            'atime' => 1366038450,
            'mtime' => 1366038450,
            'ctime' => 1365759746,
            'blksize' => -1,
            'blocks' => -1,
        );
    }
}
