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
     * Set registry object
     * 
     * @param $index
     * @param $object
     *
     * @return Block
     */
    public function setRegistryObject($index, $object)
    {
        \Bike::app()->setRegistryObject($index, $object);
        return $this;
    }

    /**
     * Get registry object
     * 
     * @param $index
     *
     * @return mixed
     */
    public function getRegistryObject($index)
    {
        return \Bike::app()->getRegistryObject($index);
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
        if (empty($block->_sections[$section])) {
            $block->_sections[$section] = array($block);
        } elseif (!is_array($block->_sections[$section])) {
            $block->_sections[$section] = array($block->_sections[$section]);
        } else {
            $block->_sections[$section][] = $block;
        }
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
     * Render template
     * 
     * @return string
     * @throws Exception
     */
    public function toHtml()
    {
        $class = get_called_class();
        if (empty($this->_template)) {
            throw new \Bike\Exception("Undeclared template in block '{$class}'");
        }
        $this->beforeToHtml();
        $classArray = explode('\\', $class);
        ob_start();
        include($classArray[0] . DS . 'templates' . DS . $this->_template);
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
        if (!isset($this->_sections[$sectionName])) {
            throw new \Bike\Exception("Section {$sectionName} does not exist in block " . get_class($this));
        }
        if (!is_array($this->_sections[$sectionName])) {
            $this->_sections[$sectionName] = array ($this->_sections[$sectionName]);
        }
        foreach ($this->_sections[$sectionName] as $section) {
            /* @var $section \Bike\Block */
            if (is_string($section)) {
                $section = call_user_func(array($section, 'create'));
            }
            echo (call_user_func(array($section, 'toHtml')));
        }
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
    
    /**
     * Append section content with self
     * 
     * @param string|Block $block
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function appendSection($block, $section)
    {
        if (!is_array($this->_sections[$section])) {
            $this->_sections[$section] = array($this->_sections[$section]);
        }
        $this->_sections[$section][] = $block;
        return $this;
    }
    
    /**
     * Prepend section content with self
     * 
     * @param string|Block $block
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function prependSection($block, $section)
    {
        $sections = array_reverse($this->_sections[$section]);
        $sections[] = $block;
        $this->_sections[$section] = $sections;
        return $this;
    }
    
    /**
     * Replace section content with self
     * 
     * @param string|Block $block
     * @param string $section
     * @throws Exception
     * @return Block
     */
    public function replaceSection($block, $section)
    {
        $this->_sections[$section] = array($block);
        return $this;
    }

    /**
     * Get document object from registry
     * 
     * @return \Bike\Html\Document
     */
    public function document()
    {
        return $this->getRegistryObject(\Bike\Html\Document::DEFAULT_REGISTRY_INDEX);
    }
}