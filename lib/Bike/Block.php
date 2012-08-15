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

abstract class Block extends \Bike\Prototypes\SingletonOverridable
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
    protected static $_sections = array();
    
    /**
     * Parental blocks
     * 
     * @var array
     */
    protected static $_parentBlocks = array();
    
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
    
    public function init()
    {
        return $this;
    }
    
    public function embed(\Bike\Block $block, $section)
    {
        $block->appendSection($section);
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
     * Append section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function appendSection($section)
    {
        if (!isset(self::$_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        self::$_sections[$section][] = $this;
        return $this;
    }
    
    /**
     * Prepend section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function prependSection($section)
    {
        if (!isset(self::$_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        $sections = array_reverse(self::$_sections[$section]);
        $sections[] = $this;
        self::$_sections[$section] = $sections;
        return $this;
    }
    
    /**
     * Replace section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function replaceSection($section)
    {
        if (!isset(self::$_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        self::$_sections[$section] = array($this);
        return $this;
    }
    
    /**
     * Create section
     * 
     * @param string $sectionName
     * @return Block
     * @throws Exception
     */
    public function sectionToHtml($sectionName)
    {
        $output = '';
        if (!empty(self::$_sections[$sectionName])) {
            foreach (self::$_sections[$sectionName] as $sectionBlock) {
                /* @var Block $sectionBlock */
                $output .= $sectionBlock->toHtml();                
            }
        }
        return $output;
    }
    
    /**
     * Render section
     * 
     * @param $sectionName
     * @return Block
     */
    public function section($sectionName)
    {
        echo $this->sectionToHtml($sectionName);
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