<?php
/**
 * $$name_placeholder_notice$$
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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Http;

class Session extends \Bike\Prototypes\SingletonOverridable
{
    public function __get($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }
    
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }
    
    public function start()
    {
        session_start();
        return $this;
    }
    
    public function commit()
    {
        session_commit();
        return $this;
    }
    
    public function close()
    {
        session_write_close();
        return $this;
    }
    
    public function setSessionName($name)
    {
        session_name($name);
        return $this;
    }
    
    public function getSessionId()
    {
        return session_id();
    }
}