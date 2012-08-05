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
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Html;

class Tag
{
    /**
     * Tag name
     * 
     * @var string
     */
    protected $_name = 'div';
    
    /**
     * Tag attributes
     * 
     * @var array
     */
    protected $_attributes = array();
    
    /**
     * Tag inner content
     * 
     * @var null
     */
    protected $_content = null;
    
    /**
     * Is short tag (enclosed)
     * @var bool
     */
    protected $_short = false;
    
    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Set tag attribute
     * 
     * @param string $name
     * @param string $value
     * @return Tag
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }
    
    /**
     * Delete tag attribute
     * 
     * @param string $name
     * @return Tag
     */
    public function delAttribute($name)
    {
        unset($this->_attributes[$name]);
        return $this;
    }
    
    /**
     * Get tag attribute
     * 
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return $this->_attributes[$name];
    }
    
    /**
     * Set tag content
     * 
     * @param string $content
     * @return Tag
     */
    public function setContent($content = null)
    {
        $this->_content = $content;
        return $this;
    }
    
    /**
     * Set is tag short flag
     * 
     * @param bool $value
     * @return Tag
     */
    public function setShort($value = false)
    {
        $this->_short = $value;
        return $this;
    }
    
    /**
     * Render tag
     * 
     * @return string
     */
    public function render()
    {
        $html = '<' . $this->_name;
        foreach ($this->_attributes as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '" ';
        }
        if ($this->_short) {
            $html .= ' />';
            return $html;
        }
        $html .= '>' . $this->_content . '</' . $this->_name . '>';
        return $html;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_name);
        unset($this->_attributes);
        unset($this->_content);
        unset($this->_short);
    }
}
