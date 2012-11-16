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

namespace Magelight\Cache\Adapters;

class File extends \Magelight\Cache\AdapterAbstract
{

    protected $_path = '/var/tmp';

    public function init(array $config = [])
    {
        $this->_path = isset($config['path'])
            ? \Magelight::app()->getAppDir() . DS . trim($config['path'], '\\/')
            : $this->_path;
        return $this;
    }

    public function getFilepath($key)
    {
        return $this->_path . DS . md5($key);
    }

    public function get($key, $default = null)
    {
        $data = @file_get_contents($this->getFilepath($key));
        if ($data === false) {
            return $default;
        }
        $data = unserialize($data);
        if (!empty($data['ttl']) && $data['ttl'] < time()) {
            return $default;
        }
        return array_key_exists('value' , $data) ? $data['value'] : $default;
    }

    public function set($key, $value, $ttl = 360)
    {
        $data = serialize(['ttl' => time() + $ttl, 'value' => $value]);
        return (bool) @file_put_contents($this->getFilepath($key) , $data);
    }

    public function del($key)
    {
        return @unlink($this->getFilepath($key));
    }

    public function clear()
    {
        return true;
    }

    public function increment($key, $incValue = 1)
    {
        $file = @fopen($this->getFilepath($key), 'r');
        @flock($file, LOCK_EX);
        $value = $this->get($file, 0) + $incValue;
        $result = $this->set($key, $value, 0);
        @flock($file, LOCK_UN);
        return $result;
    }

    public function decrement($key, $decValue = 1)
    {
        $file = @fopen($this->getFilepath($key), 'r');
        @flock($file, LOCK_EX);
        $value = $this->get($file, 0) - $decValue;
        $result = $this->set($key, $value, 0);
        @flock($file, LOCK_UN);
        return $result;
    }
}