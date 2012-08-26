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

class Response
{
    protected $_headers = array();
    
    protected $_content = null;
    
    public function __construct()
    {
           
    }
    
    public function addHeader($name = null, $value = null)
    {
        $this->_headers[] = array('name' => $name, 'value' => $value);
        return $this;
    }
    
    public function setContent($content = null)
    {
        $this->_content = $content;
        return $this;
    }
    
    public function send()
    {
        foreach ($this->_headers as $header) {
            $headerStr = '';
            if (!empty($header['name'])) {
                $headerStr .= $header['name'];
            }
            if (!empty($header['value'])) {
                $headerStr .=  ': ' . $header['value'];
            }
            header($headerStr);
        }
        echo $this->_content;
    }
}