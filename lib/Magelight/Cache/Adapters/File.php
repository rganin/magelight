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