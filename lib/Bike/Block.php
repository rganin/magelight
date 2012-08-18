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

namespace Bike;
/**
 * Block abstract
 */
abstract class Block extends \Bike\Prototypes\Overridable
{  
    /**
     * Template string path
     * 
     * @var string|null
     */
    protected $_template = null;
    
    /**
     * Template variables
     * 
     * @var array
     */
    protected $_variables = array();
    
    /**
     * View sections with children blocks
     * 
     * @var array
     */
    protected $_sections = array();
    
    /**
     * Getter
     * 
     * @param $variable
     * @return null
     */
    public function __get($variable)
    {
        if (isset($this->_variables[$variable])) {
            return $this->_variables[$variable];
        }
        return null;
    }

    /**
     * Init dummy
     * 
     * @return Block
     */
    public function init()
    {
        return $this;
    }

    /**
     * Embed block to section
     * 
     * @param string|Block $block
     * @param string $section
     *
     * @return Block
     */
    public function embed($block, $section)
    {
        $block->_sections[$section] = $block;
        return $this;
    }

    /**
     * Before to HTML dummy
     * 
     * @return Block
     */
    protected function beforeToHtml()
    {
        return $this;
    }

    /**
     * Render block to HTML
     * 
     * @return string
     */
    public function toHtml()
    {
        $this->beforeToHtml();
        $class = explode('\\', get_called_class());
        ob_start();
        include($class[0] . DS . 'templates' . DS . $this->_template);
        $this->afterToHtml();
        return ob_get_clean();
    }
    
    /**
     * After to HTML dummy
     * 
     * @return Block
     */
    protected function afterToHtml()
    {
        return $this;
    }

    /**
     * Render section by name in block
     * 
     * @param $sectionName
     *
     * @return Block
     * @throws Exception
     */
    public function section($sectionName)
    {
        /* @var $this->_sections[$sectionName] \Bike\Block */
        if (!isset($this->_sections[$sectionName])) {
            throw new \Bike\Exception("Section {$sectionName} does not exist in block " . get_class($this));
        }
        if (is_string($this->_sections[$sectionName])) {
            $this->_sections[$sectionName] = call_user_func(array($this->_sections[$sectionName], 'create'));
        }
        echo (call_user_func(array($this->_sections[$sectionName], 'toHtml')));
        return $this;
    }
    
    /**
     * Set variable value
     * 
     * @param string $varName
     * @param mixed $varValue
     * @return Block
     */
    public function set($varName, $varValue)
    {
        $this->_variables[(string) $varName] = $varValue;
        return $this;
    }
    
    /**
     * Link variable value to view internal var
     * 
     * @param string $varName
     * @param mixed $varValue
     * @return Block
     */
    public function link($varName, &$varValue)
    {
        $this->_variables[(string) $varName] = $varValue;
        return $this;
    }
    
    /**
     * Get variable value
     * 
     * @param string $varName
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($varName, $default = null)
    {
        if (isset($this->_variables[$varName])) {
            return $this->_variables[$varName];
        }
        return $default;
    }
    
    /**
     * Set template
     * 
     * @param $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }
}