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
 * @version   $$version_placeholder_notice$$
 * @author     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components\CacheAdapters;

interface AdapterInterface
{
    public function init($conpression = \Magelight\Components\Cache::COMPRESSION_OFF);
       
    public function get($key, $default = null);
    
    public function set($key, $value, $ttl = 360);
    
    public function del($key);
    
    public function clear();
    
    public function increment($key, $incValue = 1);
    
    public function decrement($key, $decValue = 1);
}